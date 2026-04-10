<?php

namespace App\Http\Controllers;

use App\Models\Edu;
use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EduImportController extends Controller
{
    // ─────────────────────────────────────────────────────────
    // SHOW IMPORT PAGE
    // ─────────────────────────────────────────────────────────
    public function index(): View
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-view'), 403, 'Accès non autorisé.');

        $filieres = Filiere::orderBy('name')->get();
        $groupes  = Groupe::with('filiere')->orderBy('id_filiere')->orderBy('name')->get();
        $history  = session('edu_import_history', []);

        return view('gestionnaire.edu-import', compact('filieres', 'groupes', 'history'));
    }

    // ─────────────────────────────────────────────────────────
    // PREVIEW / VALIDATE — returns JSON for the Step 2 panel
    // ─────────────────────────────────────────────────────────
    public function preview(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403, 'Accès non autorisé.');

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $rows   = $this->parseFile($request->file('file'));
        $result = $this->validateRows($rows);

        // Store validated data in session so confirm() can use it without re-uploading
        session(['edu_preview' => $result]);

        return response()->json($result);
    }

    // ─────────────────────────────────────────────────────────
    // CONFIRM IMPORT — process the previewed rows
    // ─────────────────────────────────────────────────────────
    public function confirm(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403, 'Accès non autorisé.');

        $preview = session('edu_preview');

        if (! $preview || empty($preview['valid_rows'])) {
            return redirect()->route('edu-import.index')
                ->with('error', 'Aucune donnée à importer. Veuillez re-uploader le fichier.');
        }

        $imported = 0;
        $skipped  = 0;

        foreach ($preview['valid_rows'] as $row) {
            if (Edu::where('edu_email', $row['edu_email'])->exists()) {
                $skipped++;
                continue;
            }

            Edu::create([
                'edu_email'    => $row['edu_email'],
                'password'     => Hash::make($row['password']),
                'nom'          => $row['nom'],
                'prenom'       => $row['prenom'],
                'filiere_code' => $row['filiere_code'],
                'groupe_code'  => $row['groupe_code'],
                'used'         => false,
            ]);

            $imported++;
        }

        $history   = session('edu_import_history', []);
        $history[] = [
            'date'     => now()->format('d M Y H:i'),
            'filename' => session('edu_import_filename', 'fichier.xlsx'),
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => count($preview['errors'] ?? []),
            'user'     => auth()->user()->name,
        ];
        session(['edu_import_history' => array_slice($history, -20)]);
        session()->forget('edu_preview');
        session()->forget('edu_import_filename');

        return redirect()->route('edu-import.index')
            ->with('import_success', [
                'imported' => $imported,
                'skipped'  => $skipped,
                'errors'   => count($preview['errors'] ?? []),
            ]);
    }

    // ─────────────────────────────────────────────────────────
    // MANUAL ADD (single stagiaire)
    // ─────────────────────────────────────────────────────────
    public function manualStore(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403, 'Accès non autorisé.');

        $data = $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'edu_email'    => 'required|email|unique:edu,edu_email',
            'password'     => 'required|string|min:6',
            'filiere_code' => 'required|exists:filieres,code',
            'groupe_code'  => 'required|exists:groupes,code',
        ]);

        Edu::create([
            'nom'          => $data['nom'],
            'prenom'       => $data['prenom'],
            'edu_email'    => $data['edu_email'],
            'password'     => Hash::make($data['password']),
            'filiere_code' => $data['filiere_code'],
            'groupe_code'  => $data['groupe_code'],
            'used'         => false,
        ]);

        return redirect()->route('edu-import.index')
            ->with('success', "Stagiaire {$data['prenom']} {$data['nom']} ajouté avec succès.");
    }

    // ─────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE
    // ─────────────────────────────────────────────────────────
    public function downloadTemplate()
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-view'), 403, 'Accès non autorisé.');

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        $headers = ['edu_email', 'password', 'nom', 'prenom', 'filiere_code', 'groupe_code'];
        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E293B']],
                'alignment' => ['horizontal' => 'center'],
            ]);
            $sheet->getColumnDimensionByColumn($col + 1)->setWidth(22);
        }

        $examples = [
            ['ahmed.ali@ofppt.ma',     'pass1234', 'Ali',     'Ahmed',   'DEVDIG', 'DD-G1A'],
            ['sara.idrissi@ofppt.ma',  'pass5678', 'Idrissi', 'Sara',    'GI',     'GI-G1C'],
            ['youssef.malik@ofppt.ma', 'pass9012', 'Malik',   'Youssef', 'DEVDIG', 'DD-G1B'],
        ];

        foreach ($examples as $rowIdx => $row) {
            foreach ($row as $col => $val) {
                $sheet->setCellValue(chr(65 + $col) . ($rowIdx + 2), $val);
            }
        }

        $writer   = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'modele_import_edu.xlsx';

        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // PRIVATE: PARSE FILE
    // ─────────────────────────────────────────────────────────
    private function parseFile($file): array
    {
        session(['edu_import_filename' => $file->getClientOriginalName()]);

        $extension = strtolower($file->getClientOriginalExtension());
        $rows      = [];

        if ($extension === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            $header = null;
            while (($line = fgetcsv($handle, 1000, ',')) !== false) {
                if (! $header) {
                    $header = array_map('trim', $line);
                    continue;
                }
                if (count($line) >= 6) {
                    $rows[] = array_combine(
                        ['edu_email', 'password', 'nom', 'prenom', 'filiere_code', 'groupe_code'],
                        array_slice(array_map('trim', $line), 0, 6)
                    );
                }
            }
            fclose($handle);
        } else {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();
            $highestRow  = $sheet->getHighestDataRow();

            for ($row = 2; $row <= $highestRow; $row++) {
                $data = [];
                for ($col = 1; $col <= 6; $col++) {
                    $data[] = trim((string) $sheet->getCell(
                        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row
                    )->getValue());
                }
                if (array_filter($data)) {
                    $rows[] = array_combine(
                        ['edu_email', 'password', 'nom', 'prenom', 'filiere_code', 'groupe_code'],
                        $data
                    );
                }
            }
        }

        return $rows;
    }

    // ─────────────────────────────────────────────────────────
    // PRIVATE: VALIDATE ROWS
    // ─────────────────────────────────────────────────────────
    private function validateRows(array $rows): array
    {
        $validRows    = [];
        $warnings     = [];
        $errors       = [];
        $skippedLines = [];

        $filiereCodes = Filiere::pluck('id', 'code')->toArray();
        $groupeCodes  = Groupe::pluck('id', 'code')->toArray();

        foreach ($rows as $lineNum => $row) {
            $line   = $lineNum + 2;
            $email  = $row['edu_email'] ?? '';
            $pass   = $row['password']  ?? '';
            $nom    = $row['nom']       ?? '';
            $prenom = $row['prenom']    ?? '';
            $fc     = strtoupper(trim($row['filiere_code'] ?? ''));
            $gc     = trim($row['groupe_code'] ?? '');

            // Hard errors — skip row
            if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ligne {$line} — Email invalide ou manquant : «{$email}»";
                continue;
            }
            if (! isset($filiereCodes[$fc])) {
                $errors[] = "Ligne {$line} — Code filière «{$fc}» introuvable en base";
                continue;
            }
            if (! isset($groupeCodes[$gc])) {
                $errors[] = "Ligne {$line} — Code groupe «{$gc}» introuvable en base";
                continue;
            }

            // Warnings — import anyway
            if (Edu::where('edu_email', $email)->exists()) {
                $warnings[]     = "Ligne {$line} — Email «{$email}» déjà présent (sera ignoré à l'import)";
                $skippedLines[] = $line;
                continue;
            }
            if (strlen($pass) < 6) {
                $warnings[] = "Ligne {$line} — Mot de passe court (< 6 car.) pour «{$email}» — importé quand même";
            }
            if (empty($nom) || empty($prenom)) {
                $warnings[] = "Ligne {$line} — Nom ou prénom manquant pour «{$email}»";
            }

            $validRows[] = [
                'edu_email'    => $email,
                'password'     => $pass ?: 'ofppt2025',
                'nom'          => $nom,
                'prenom'       => $prenom,
                'filiere_code' => $fc,
                'groupe_code'  => $gc,
            ];
        }

        return [
            'total'       => count($rows),
            'valid'       => count($validRows),
            'warn_count'  => count($warnings),
            'error_count' => count($errors),
            'valid_rows'  => $validRows,
            'warnings'    => $warnings,
            'errors'      => $errors,
            'skipped'     => $skippedLines,
        ];
    }
}