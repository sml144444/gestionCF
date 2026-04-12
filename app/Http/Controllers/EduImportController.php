<?php

namespace App\Http\Controllers;

use App\Models\Edu;
use App\Models\EduImportLog;
use App\Models\Filiere;
use App\Models\Groupe;
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

        // ── Filters — use input() NOT string() to avoid truthy Stringable bug ──
        $filterSearch   = trim($request->input('search',         ''));
        $filterFiliere  = trim($request->input('filiere_code',   ''));
        $filterGroupe   = trim($request->input('groupe_code',    ''));
        $filterStatut   = trim($request->input('statut',         ''));
        $filterAnnee    = trim($request->input('annee_scolaire', ''));
        $filterDateFrom = trim($request->input('date_from',      ''));
        $filterDateTo   = trim($request->input('date_to',        ''));

        // ── Build query — each condition only runs when value is non-empty string ──
        $query = Edu::latest();

        if ($filterSearch !== '') {
            $query->where(fn($q) =>
                $q->where('edu_email', 'like', "%{$filterSearch}%")
                  ->orWhere('nom',     'like', "%{$filterSearch}%")
                  ->orWhere('prenom',  'like', "%{$filterSearch}%")
            );
        }

        if ($filterFiliere !== '') {
            $query->where('filiere_code', $filterFiliere);
        }

        if ($filterGroupe !== '') {
            $query->where('groupe_code', $filterGroupe);
        }

        if ($filterStatut === 'used') {
            $query->where('used', true);
        } elseif ($filterStatut === 'pending') {
            $query->where('used', false);
        }

        // Academic year: "2025/2026" → between 2025-09-01 and 2026-08-31
        if ($filterAnnee !== '' && preg_match('/^(\d{4})\/(\d{4})$/', $filterAnnee, $m)) {
            $query->whereBetween('created_at', [
                $m[1] . '-09-01 00:00:00',
                $m[2] . '-08-31 23:59:59',
            ]);
        }

        if ($filterDateFrom !== '') {
            $query->whereDate('created_at', '>=', $filterDateFrom);
        }

        if ($filterDateTo !== '') {
            $query->whereDate('created_at', '<=', $filterDateTo);
        }

        $eduAccounts = $query->paginate(25, ['*'], 'edu_page')->withQueryString();

        // ── Global stats (always unfiltered) ──
        $eduStats = [
            'total'   => Edu::count(),
            'used'    => Edu::where('used', true)->count(),
            'pending' => Edu::where('used', false)->count(),
        ];

        $hasFilters = $filterSearch !== '' || $filterFiliere !== '' || $filterGroupe !== ''
                   || $filterStatut !== '' || $filterAnnee  !== ''
                   || $filterDateFrom !== '' || $filterDateTo !== '';

        $anneesScolaires = $this->getAnneesScolaires();

        // Distinct codes in edu table for dropdown options
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
    // CONFIRM IMPORT
    // ─────────────────────────────────────────────────────────
    public function confirm(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);
        $preview = session('edu_preview');
        if (! $preview || empty($preview['valid_rows'])) {
            return redirect()->route('edu-import.index')
                ->with('error', 'Aucune donnée à importer. Veuillez re-uploader le fichier.');
        }
        $imported = 0; $skipped = 0;
        foreach ($preview['valid_rows'] as $row) {
            if (Edu::where('edu_email', $row['edu_email'])->exists()) { $skipped++; continue; }
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
        EduImportLog::create([
            'id_user'  => auth()->id(),
            'filename' => session('edu_import_filename', 'fichier.xlsx'),
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => count($preview['errors'] ?? []),
        ]);
        session()->forget(['edu_preview', 'edu_import_filename']);
        return redirect()->route('edu-import.index', ['tab' => 'accounts'])
            ->with('import_success', ['imported' => $imported, 'skipped' => $skipped, 'errors' => count($preview['errors'] ?? [])]);
    }

    // ─────────────────────────────────────────────────────────
    // MANUAL ADD
    // ─────────────────────────────────────────────────────────
    public function manualStore(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-import'), 403);
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
        EduImportLog::create([
            'id_user'  => auth()->id(),
            'filename' => 'Ajout manuel',
            'imported' => 1, 'skipped' => 0, 'errors' => 0,
        ]);
        return redirect()->route('edu-import.index', ['tab' => 'accounts'])
            ->with('success', "Stagiaire {$data['prenom']} {$data['nom']} ajouté avec succès.");
    }

    // ─────────────────────────────────────────────────────────
    // DOWNLOAD TEMPLATE
    // ─────────────────────────────────────────────────────────
    public function downloadTemplate()
    {
        abort_unless(auth()->user()->hasPermissionTo('edu-view'), 403);
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $headers = ['edu_email','password','nom','prenom','filiere_code','groupe_code'];
        foreach ($headers as $col => $header) {
            $cell = chr(65+$col).'1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray(['font'=>['bold'=>true,'color'=>['argb'=>'FFFFFFFF']],'fill'=>['fillType'=>'solid','startColor'=>['argb'=>'FF1E293B']],'alignment'=>['horizontal'=>'center']]);
            $sheet->getColumnDimensionByColumn($col+1)->setWidth(22);
        }
        foreach ([['ahmed.ali@ofppt.ma','pass1234','Ali','Ahmed','DEVDIG','DD-G1A'],['sara.idrissi@ofppt.ma','pass5678','Idrissi','Sara','GI','GI-G1C']] as $ri => $row) {
            foreach ($row as $col => $val) $sheet->setCellValue(chr(65+$col).($ri+2), $val);
        }
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        ob_start(); $writer->save('php://output'); $content = ob_get_clean();
        return response($content, 200, ['Content-Type'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','Content-Disposition'=>'attachment; filename="modele_import_edu.xlsx"']);
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

    private function parseFile($file): array
    {
        session(['edu_import_filename' => $file->getClientOriginalName()]);
        $rows = [];
        if (strtolower($file->getClientOriginalExtension()) === 'csv') {
            $handle = fopen($file->getRealPath(), 'r'); $header = null;
            while (($line = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) { $header = array_map('trim',$line); continue; }
                if (count($line) >= 6) $rows[] = array_combine(['edu_email','password','nom','prenom','filiere_code','groupe_code'], array_slice(array_map('trim',$line),0,6));
            }
            fclose($handle);
        } else {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            for ($row = 2; $row <= $sheet->getHighestDataRow(); $row++) {
                $data = [];
                for ($col = 1; $col <= 6; $col++) $data[] = trim((string)$sheet->getCell(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col).$row)->getValue());
                if (array_filter($data)) $rows[] = array_combine(['edu_email','password','nom','prenom','filiere_code','groupe_code'], $data);
            }
        }
        return $rows;
    }

    private function validateRows(array $rows): array
    {
        $validRows=[]; $warnings=[]; $errors=[]; $skippedLines=[];
        $filiereCodes = Filiere::pluck('id','code')->toArray();
        $groupeCodes  = Groupe::pluck('id','code')->toArray();
        foreach ($rows as $lineNum => $row) {
            $line=$lineNum+2; $email=$row['edu_email']??''; $pass=$row['password']??'';
            $nom=$row['nom']??''; $prenom=$row['prenom']??'';
            $fc=strtoupper(trim($row['filiere_code']??'')); $gc=trim($row['groupe_code']??'');
            if (empty($email)||!filter_var($email,FILTER_VALIDATE_EMAIL)) { $errors[]="Ligne {$line} — Email invalide : «{$email}»"; continue; }
            if (!isset($filiereCodes[$fc])) { $errors[]="Ligne {$line} — Code filière «{$fc}» introuvable"; continue; }
            if (!isset($groupeCodes[$gc]))  { $errors[]="Ligne {$line} — Code groupe «{$gc}» introuvable"; continue; }
            if (Edu::where('edu_email',$email)->exists()) { $warnings[]="Ligne {$line} — «{$email}» déjà présent"; $skippedLines[]=$line; continue; }
            if (strlen($pass)<6) $warnings[]="Ligne {$line} — Mot de passe court pour «{$email}»";
            if (empty($nom)||empty($prenom)) $warnings[]="Ligne {$line} — Nom/prénom manquant pour «{$email}»";
            $validRows[]=['edu_email'=>$email,'password'=>$pass?:'ofppt2025','nom'=>$nom,'prenom'=>$prenom,'filiere_code'=>$fc,'groupe_code'=>$gc];
        }
        return ['total'=>count($rows),'valid'=>count($validRows),'warn_count'=>count($warnings),'error_count'=>count($errors),'valid_rows'=>$validRows,'warnings'=>$warnings,'errors'=>$errors,'skipped'=>$skippedLines];
    }
}