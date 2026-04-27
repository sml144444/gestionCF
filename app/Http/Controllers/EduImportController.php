<?php

namespace App\Http\Controllers;

use App\Models\Edu;
use App\Models\EduImportLog;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory;

class EduImportController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-view'), 403, 'Accès non autorisé.');

        $filieres = Filiere::orderBy('name')->get();
        $groupes  = Groupe::with('filiere')->orderBy('id_filiere')->orderBy('name')->get();

        $history = EduImportLog::with('user')->latest()->take(50)->get();

        // ── Filters ──
        $filterSearch   = trim($request->input('search',         ''));
        $filterFiliere  = trim($request->input('filiere_code',   ''));
        $filterGroupe   = trim($request->input('groupe_code',    ''));
        $filterStatut   = trim($request->input('statut',         ''));
        $filterAnnee    = trim($request->input('annee_scolaire', ''));
        $filterDateFrom = trim($request->input('date_from',      ''));
        $filterDateTo   = trim($request->input('date_to',        ''));

        $query = Edu::latest();

        if ($filterSearch !== '') {
            $query->where(fn($q) =>
                $q->where('edu_email', 'like', "%{$filterSearch}%")
                  ->orWhere('nom',     'like', "%{$filterSearch}%")
                  ->orWhere('prenom',  'like', "%{$filterSearch}%")
            );
        }
        if ($filterFiliere !== '') $query->where('filiere_code', $filterFiliere);
        if ($filterGroupe  !== '') $query->where('groupe_code',  $filterGroupe);

        if ($filterStatut === 'used')        $query->where('used', true);
        elseif ($filterStatut === 'pending') $query->where('used', false);

        if ($filterAnnee !== '' && preg_match('/^(\d{4})\/(\d{4})$/', $filterAnnee, $m)) {
            $query->whereBetween('created_at', [
                $m[1] . '-09-01 00:00:00',
                $m[2] . '-08-31 23:59:59',
            ]);
        }
        if ($filterDateFrom !== '') $query->whereDate('created_at', '>=', $filterDateFrom);
        if ($filterDateTo   !== '') $query->whereDate('created_at', '<=', $filterDateTo);

        $eduAccounts = $query->paginate(25, ['*'], 'edu_page')->withQueryString();

        $eduStats = [
            'total'   => Edu::count(),
            'used'    => Edu::where('used', true)->count(),
            'pending' => Edu::where('used', false)->count(),
        ];

        $hasFilters = $filterSearch !== '' || $filterFiliere !== '' || $filterGroupe !== ''
                   || $filterStatut !== '' || $filterAnnee  !== ''
                   || $filterDateFrom !== '' || $filterDateTo !== '';

        $anneesScolaires = $this->getAnneesScolaires();

        $eduFiliereCodes = Edu::select('filiere_code')
            ->whereNotNull('filiere_code')
            ->distinct()->orderBy('filiere_code')->pluck('filiere_code');

        $eduGroupeCodes = Edu::select('groupe_code')
            ->whereNotNull('groupe_code')
            ->when($filterFiliere !== '', fn($q) => $q->where('filiere_code', $filterFiliere))
            ->distinct()->orderBy('groupe_code')->pluck('groupe_code');

        $activeTab = trim($request->input('tab', 'import'));
        if (! auth()->user()->can('edu-import') && $activeTab === 'import') {
            $activeTab = 'accounts';
        }

        return view('gestionnaire.edu-import', compact(
            'filieres', 'groupes', 'history',
            'eduAccounts', 'eduStats',
            'filterSearch', 'filterFiliere', 'filterGroupe',
            'filterStatut', 'filterAnnee', 'filterDateFrom', 'filterDateTo',
            'anneesScolaires', 'eduFiliereCodes', 'eduGroupeCodes',
            'hasFilters', 'activeTab'
        ));
    }

    // ─────────────────────────────────────────────────────────
    // EDIT
    // ─────────────────────────────────────────────────────────
    public function edit(Edu $edu): View
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);

        $filieres = Filiere::orderBy('name')->get();
        $groupes  = Groupe::with('filiere')->orderBy('id_filiere')->orderBy('name')->get();

        return view('gestionnaire.edu-edit', compact('edu', 'filieres', 'groupes'));
    }

    // ─────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────
    public function update(Request $request, Edu $edu): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);

        $data = $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'edu_email'    => 'required|email|unique:edu,edu_email,' . $edu->id,
            'filiere_code' => 'required|exists:filieres,code',
            'groupe_code'  => 'required|exists:groupes,code',
            'password'     => 'nullable|string|min:6',
        ]);

        $groupeAppartient = Groupe::join('filieres', 'groupes.id_filiere', '=', 'filieres.id')
            ->where('groupes.code', $data['groupe_code'])
            ->where('filieres.code', $data['filiere_code'])
            ->exists();

        if (! $groupeAppartient) {
            return back()
                ->withErrors(['groupe_code' => "Le groupe «{$data['groupe_code']}» n'appartient pas à la filière «{$data['filiere_code']}»."])
                ->withInput();
        }

        $edu->nom          = $data['nom'];
        $edu->prenom       = $data['prenom'];
        $edu->edu_email    = $data['edu_email'];
        $edu->filiere_code = $data['filiere_code'];
        $edu->groupe_code  = $data['groupe_code'];
        if (! empty($data['password'])) {
            $edu->password = Hash::make($data['password']);
        }
        $edu->save();

        return redirect()->route('edu-import.index', ['tab' => 'accounts'])
            ->with('success', "Compte de {$edu->prenom} {$edu->nom} mis à jour.");
    }

    // ─────────────────────────────────────────────────────────
    // DELETE — cascade suppression stagiaire si compte actif
    // ─────────────────────────────────────────────────────────
    public function destroy(Edu $edu): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);

        $name = "{$edu->prenom} {$edu->nom}";
        $deletedStagiaire = false;

        if ($edu->used) {
            $stagiaire = User::where('email', $edu->edu_email)
                             ->where('role', 'stagiaire')
                             ->first();
            if ($stagiaire) {
                $stagiaire->delete();
                $deletedStagiaire = true;
            }
        }

        $edu->delete();

        $message = "Compte EDU de {$name} supprimé.";
        if ($deletedStagiaire) {
            $message .= " Le compte stagiaire associé a également été supprimé.";
        }

        return redirect()->route('edu-import.index', ['tab' => 'accounts'])
            ->with('success', $message);
    }

    // ─────────────────────────────────────────────────────────
    // PREVIEW / VALIDATE
    // ─────────────────────────────────────────────────────────
    public function preview(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);

        $request->validate(['file' => 'required|file|mimes:xlsx,xls,csv|max:5120']);

        $rows   = $this->parseFile($request->file('file'));
        $result = $this->validateRows($rows);

        session(['edu_preview' => $result]);

        return response()->json($result);
    }

    // ─────────────────────────────────────────────────────────
    // CONFIRM IMPORT — ✅ NO EMAIL SENT
    // ─────────────────────────────────────────────────────────
    public function confirm(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);

        $preview = session('edu_preview');
        if (! $preview || empty($preview['valid_rows'])) {
            return redirect()->route('edu-import.index')
                ->with('error', 'Aucune donnée à importer. Veuillez re-uploader le fichier.');
        }

        $log = EduImportLog::create([
            'id_user'  => auth()->id(),
            'filename' => session('edu_import_filename', 'fichier.xlsx'),
            'imported' => 0,
            'skipped'  => 0,
            'errors'   => count($preview['errors'] ?? []),
        ]);

        $imported = 0;
        $skipped  = 0;

        foreach ($preview['valid_rows'] as $row) {
            if (Edu::where('edu_email', $row['edu_email'])->exists()) {
                $skipped++;
                continue;
            }

            $plainPassword = $row['plain_password'];

            Edu::create([
                'edu_email'         => $row['edu_email'],
                'password'          => Hash::make($plainPassword),
                'nom'               => $row['nom'],
                'prenom'            => $row['prenom'],
                'filiere_code'      => $row['filiere_code'],
                'groupe_code'       => $row['groupe_code'],
                'used'              => false,
                'edu_import_log_id' => $log->id,
            ]);

            $imported++;
        }

        $log->update(['imported' => $imported, 'skipped' => $skipped]);

        session()->forget(['edu_preview', 'edu_import_filename']);

        return redirect()->route('edu-import.index', ['tab' => 'accounts'])
            ->with('import_success', [
                'imported' => $imported,
                'skipped'  => $skipped,
                'errors'   => count($preview['errors'] ?? []),
            ]);
    }

    // ─────────────────────────────────────────────────────────
    // MANUAL ADD — ✅ password required, no email sent
    // ─────────────────────────────────────────────────────────
    public function manualStore(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);

        $data = $request->validate([
            'nom'          => 'required|string|max:100',
            'prenom'       => 'required|string|max:100',
            'edu_email'    => 'required|email|unique:edu,edu_email',
            'filiere_code' => 'required|exists:filieres,code',
            'groupe_code'  => 'required|exists:groupes,code',
            'password'     => 'required|string|min:6',
        ]);

        $groupeAppartient = Groupe::join('filieres', 'groupes.id_filiere', '=', 'filieres.id')
            ->where('groupes.code', $data['groupe_code'])
            ->where('filieres.code', $data['filiere_code'])
            ->exists();

        if (! $groupeAppartient) {
            return back()
                ->withErrors(['groupe_code' => "Le groupe «{$data['groupe_code']}» n'appartient pas à la filière «{$data['filiere_code']}»."])
                ->withInput();
        }

        // Vérification capacité groupe
        $groupe = Groupe::where('code', $data['groupe_code'])
                        ->withCount('stagiaires')
                        ->first();

        if ($groupe && $groupe->stagiaires_count >= $groupe->nbr_limit) {
            return back()
                ->withErrors([
                    'groupe_code' => "Le groupe «{$data['groupe_code']}» est complet ({$groupe->stagiaires_count}/{$groupe->nbr_limit} places). Choisissez un autre groupe.",
                ])
                ->withInput();
        }

        $log = EduImportLog::create([
            'id_user'  => auth()->id(),
            'filename' => 'Ajout manuel',
            'imported' => 1,
            'skipped'  => 0,
            'errors'   => 0,
        ]);

        Edu::create([
            'nom'               => $data['nom'],
            'prenom'            => $data['prenom'],
            'edu_email'         => $data['edu_email'],
            'password'          => Hash::make($data['password']),
            'filiere_code'      => $data['filiere_code'],
            'groupe_code'       => $data['groupe_code'],
            'used'              => false,
            'edu_import_log_id' => $log->id,
        ]);

        return redirect()->route('edu-import.index', ['tab' => 'accounts'])
            ->with('success', "Stagiaire {$data['prenom']} {$data['nom']} ajouté avec succès.");
    }

    // ─────────────────────────────────────────────────────────
    // SHOW LOG — détails d'un import (AJAX JSON)
    // ─────────────────────────────────────────────────────────
    public function showLog(EduImportLog $log): \Illuminate\Http\JsonResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-view'), 403);

        $accounts = $log->eduAccounts()
            ->select('id', 'nom', 'prenom', 'edu_email', 'filiere_code', 'groupe_code', 'used', 'created_at')
            ->orderBy('nom')
            ->get();

        return response()->json([
            'log'      => [
                'id'         => $log->id,
                'filename'   => $log->filename,
                'imported'   => $log->imported,
                'skipped'    => $log->skipped,
                'errors'     => $log->errors,
                'created_at' => $log->created_at->format('d M Y H:i'),
                'user'       => $log->user?->name ?? 'Inconnu',
            ],
            'accounts' => $accounts,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE — 6 colonnes AVEC mot de passe (obligatoire)
    // ─────────────────────────────────────────────────────────
    public function downloadTemplate()
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-view'), 403);

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();

        // ✅ 6 colonnes
        $headers = ['edu_email', 'nom', 'prenom', 'filiere_code', 'groupe_code', 'password'];

        foreach ($headers as $col => $header) {
            $cell = chr(65 + $col) . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray([
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1E293B']],
                'alignment' => ['horizontal' => 'center'],
            ]);
            $sheet->getColumnDimensionByColumn($col + 1)->setWidth(24);
        }

        // ✅ Exemples avec codes réels du seeder et password obligatoire
        $examples = [
            ['ahmed.alami@ofppt.ma',  'Alami',   'Ahmed', 'DEVDIG', 'TDEV-101', 'MonPass123!'],
            ['sara.idrissi@ofppt.ma', 'Idrissi', 'Sara',  'GI',     'TGI-101',  'Sara2024!'],
        ];

        foreach ($examples as $ri => $row) {
            foreach ($row as $col => $val) {
                $sheet->setCellValue(chr(65 + $col) . ($ri + 2), $val);
            }
        }

        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="modele_import_edu.xlsx"',
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────────

    private function getAnneesScolaires(): array
    {
        $years = [];
        for ($y = 2023; $y <= (int) now()->format('Y') + 1; $y++) {
            $years[] = $y . '/' . ($y + 1);
        }
        return array_reverse($years);
    }

    /**
     * Parse uploaded file — 6 colonnes (password obligatoire).
     */
    private function parseFile($file): array
    {
        session(['edu_import_filename' => $file->getClientOriginalName()]);

        $rows = [];

        if (strtolower($file->getClientOriginalExtension()) === 'csv') {
            $handle = fopen($file->getRealPath(), 'r');
            $header = null;
            while (($line = fgetcsv($handle, 1000, ',')) !== false) {
                if (! $header) {
                    $header = array_map('trim', $line);
                    continue;
                }
                if (count($line) >= 6) {
                    $mapped = array_map('trim', $line);
                    $rows[] = [
                        'edu_email'    => $mapped[0] ?? '',
                        'nom'          => $mapped[1] ?? '',
                        'prenom'       => $mapped[2] ?? '',
                        'filiere_code' => $mapped[3] ?? '',
                        'groupe_code'  => $mapped[4] ?? '',
                        'password'     => $mapped[5] ?? '',
                    ];
                }
            }
            fclose($handle);
        } else {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet       = $spreadsheet->getActiveSheet();

            for ($row = 2; $row <= $sheet->getHighestDataRow(); $row++) {
                $data6 = [];
                for ($col = 1; $col <= 6; $col++) {
                    $data6[] = trim((string) $sheet
                        ->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row)
                        ->getValue());
                }
                if (array_filter($data6)) {
                    $rows[] = [
                        'edu_email'    => $data6[0],
                        'nom'          => $data6[1],
                        'prenom'       => $data6[2],
                        'filiere_code' => $data6[3],
                        'groupe_code'  => $data6[4],
                        'password'     => $data6[5] ?? '',
                    ];
                }
            }
        }

        return $rows;
    }

    /**
     * Validate rows — password OBLIGATOIRE.
     */
    private function validateRows(array $rows): array
    {
        $validRows    = [];
        $warnings     = [];
        $errors       = [];
        $skippedLines = [];

        $filiereCodes = Filiere::pluck('id', 'code')->toArray();
        $groupeCodes  = Groupe::pluck('id', 'code')->toArray();

        $groupeFiliereMap = Groupe::join('filieres', 'groupes.id_filiere', '=', 'filieres.id')
            ->select('groupes.code as groupe_code', 'filieres.code as filiere_code')
            ->get()
            ->pluck('filiere_code', 'groupe_code')
            ->toArray();

        $groupeCapacities = Groupe::withCount('stagiaires')
            ->get()
            ->keyBy('code')
            ->map(fn($g) => [
                'limit' => $g->nbr_limit,
                'count' => $g->stagiaires_count,
            ])
            ->toArray();

        $groupeAddedCount = [];
        $seenEmails = [];

        foreach ($rows as $lineNum => $row) {
            $line   = $lineNum + 2;
            $email  = $row['edu_email']    ?? '';
            $nom    = $row['nom']          ?? '';
            $prenom = $row['prenom']       ?? '';
            $fc     = strtoupper(trim($row['filiere_code'] ?? ''));
            $gc     = trim($row['groupe_code'] ?? '');
            $rawPassword = trim($row['password'] ?? '');

            if (empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Ligne {$line} — Email invalide : «{$email}»";
                continue;
            }
            if (! isset($filiereCodes[$fc])) {
                $errors[] = "Ligne {$line} — Code filière «{$fc}» introuvable";
                continue;
            }
            if (! isset($groupeCodes[$gc])) {
                $errors[] = "Ligne {$line} — Code groupe «{$gc}» introuvable";
                continue;
            }
            if (! isset($groupeFiliereMap[$gc]) || $groupeFiliereMap[$gc] !== $fc) {
                $errors[] = "Ligne {$line} — Le groupe «{$gc}» n'appartient pas à la filière «{$fc}»";
                continue;
            }
            if (isset($seenEmails[$email])) {
                $errors[]       = "Ligne {$line} — «{$email}» est en doublon (déjà à la ligne {$seenEmails[$email]})";
                $skippedLines[] = $line;
                continue;
            }
            $seenEmails[$email] = $line;

            if (Edu::where('edu_email', $email)->exists()) {
                $errors[]       = "Ligne {$line} — «{$email}» est déjà présent dans la base";
                $skippedLines[] = $line;
                continue;
            }
            if (empty($nom) || empty($prenom)) {
                $errors[] = "Ligne {$line} — Nom/prénom manquant pour «{$email}»";
                continue;
            }

            // ✅ Vérification capacité groupe
            if (isset($groupeCapacities[$gc])) {
                $currentCount = $groupeCapacities[$gc]['count'] + ($groupeAddedCount[$gc] ?? 0);
                $limit        = $groupeCapacities[$gc]['limit'];

                if ($currentCount >= $limit) {
                    $errors[]       = "Ligne {$line} — Le groupe «{$gc}» est complet ({$currentCount}/{$limit} places). «{$email}» ignoré.";
                    $skippedLines[] = $line;
                    continue;
                }
            }

            // ✅ Incrémenter le compteur local pour ce groupe
            $groupeAddedCount[$gc] = ($groupeAddedCount[$gc] ?? 0) + 1;

            // ✅ Password OBLIGATOIRE
            if ($rawPassword === '') {
                $errors[] = "Ligne {$line} — Mot de passe manquant pour «{$email}»";
                continue;
            }

            $validRows[] = [
                'edu_email'      => $email,
                'plain_password' => $rawPassword,
                'nom'            => $nom,
                'prenom'         => $prenom,
                'filiere_code'   => $fc,
                'groupe_code'    => $gc,
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