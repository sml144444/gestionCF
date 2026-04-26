<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRetard;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AbsenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->can('absence-view')) {
                abort(403);
            }
            return $next($request);
        });
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user       = Auth::user();
        $canViewAll = $user->can('absence-view-all')
                   || in_array($user->role, ['admin', 'gestionnaire', 'formateur']);

        $baseQuery = AbsenceRetard::with([
            'stagiaire.groupe',
            'cours.emploiDuTemps.module',
            'cours.emploiDuTemps.groupe',
            'cours.emploiDuTemps.salle',
            'cours.emploiDuTemps.gestionnaire',
        ])->where('type', 'absence');

        if (!$canViewAll) {
            $baseQuery->where('id_user', $user->id);
        }

        if ($request->filled('justifie') && in_array($request->justifie, ['0', '1', 'pending'])) {
            if ($request->justifie === 'pending') {
                $baseQuery->where('justifie', false)->whereNotNull('file_justification');
            } else {
                $baseQuery->where('justifie', (bool) $request->justifie);
            }
        }

        if ($canViewAll) {
            if ($request->filled('stagiaire_id')) {
                $baseQuery->where('id_user', $request->stagiaire_id);
            }
            if ($request->filled('groupe_id')) {
                $baseQuery->whereHas('stagiaire', fn($q) =>
                    $q->where('id_groupe', $request->groupe_id)
                );
            }
            if ($request->filled('filiere_id') && !$request->filled('groupe_id')) {
                $baseQuery->whereHas('stagiaire.groupe', fn($q) =>
                    $q->where('id_filiere', $request->filiere_id)
                );
            }
        }

        if ($request->filled('session_part')
            && in_array($request->session_part, ['s1', 's2', 's3', 's4'])) {
            $baseQuery->where('session_part', $request->session_part);
        }

        // ── Stats ──────────────────────────────────────────────
        $statsQuery = AbsenceRetard::where('type', 'absence');
        if (!$canViewAll) { $statsQuery->where('id_user', $user->id); }
        if ($canViewAll && $request->filled('stagiaire_id')) { $statsQuery->where('id_user', $request->stagiaire_id); }
        if ($canViewAll && $request->filled('groupe_id')) {
            $statsQuery->whereHas('stagiaire', fn($q) => $q->where('id_groupe', $request->groupe_id));
        }
        if ($canViewAll && $request->filled('filiere_id') && !$request->filled('groupe_id')) {
            $statsQuery->whereHas('stagiaire.groupe', fn($q) =>
                $q->where('id_filiere', $request->filiere_id)
            );
        }

        $stats = [
            'total'            => (clone $statsQuery)->count(),
            'justifies'        => (clone $statsQuery)->where('justifie', true)->count(),
            'injustifies'      => (clone $statsQuery)->where('justifie', false)->whereNull('file_justification')->count(),
            'en_attente'       => (clone $statsQuery)->where('justifie', false)->whereNotNull('file_justification')->count(),
            'total_heures_abs' => round((clone $statsQuery)->sum('duree'), 1),
            's1' => (clone $statsQuery)->where('session_part', 's1')->count(),
            's2' => (clone $statsQuery)->where('session_part', 's2')->count(),
            's3' => (clone $statsQuery)->where('session_part', 's3')->count(),
            's4' => (clone $statsQuery)->where('session_part', 's4')->count(),
        ];

        // ── Dropdowns ──────────────────────────────────────────
        $filieres   = $canViewAll ? Filiere::orderBy('name')->get() : collect();
        $fId        = $request->filled('filiere_id') ? $request->filiere_id : null;
        $groupes    = $canViewAll
            ? Groupe::when($fId, fn($q) => $q->where('id_filiere', $fId))->orderBy('name')->get()
            : collect();
        $stagiaires = collect();
        if ($canViewAll) {
            $gId        = $request->filled('groupe_id') ? $request->groupe_id : null;
            $stagiaires = User::where('role', 'stagiaire')
                ->when($gId,  fn($q) => $q->where('id_groupe', $gId))
                ->when($fId && !$gId, fn($q) => $q->whereHas('groupe', fn($g) => $g->where('id_filiere', $fId)))
                ->orderBy('name')->get();
        }

        $filterStagiaire = ($canViewAll && $request->filled('stagiaire_id'))
            ? User::find($request->stagiaire_id)
            : ($canViewAll ? null : $user);

        // ── Admin historique grouped by (date + stagiaire) ─────
        $absences        = collect();
        $absencesGrouped = collect();

        if ($canViewAll) {
            $allRecords = (clone $baseQuery)->orderByDesc('date_event')->get();

            $grouped = $allRecords
                ->groupBy(fn($a) =>
                    ($a->date_event?->format('Y-m-d') ?? 'x') . '_' . $a->id_user
                )
                ->map(function ($group) {
                    $first   = $group->first();
                    $emplois = $group->map(fn($a) => $a->cours?->emploiDuTemps)
                        ->filter()->unique('id')->values();

                    return (object) [
                        'date'               => $first->date_event,
                        'stagiaire'          => $first->stagiaire,
                        'groupe'             => $first->stagiaire?->groupe ?? $emplois->first()?->groupe,
                        'total_duree'        => round($group->sum('duree'), 1),
                        'parts'              => $group->pluck('session_part')->filter()->unique()->sort()->values()->toArray(),
                        'emplois'            => $emplois,
                        'modules'            => $emplois->map(fn($e) => $e?->module)->filter()->unique('id')->values(),
                        'formateurs'         => $emplois->map(fn($e) => $e?->gestionnaire)->filter()->unique('id')->values(),
                        'is_justified'       => $group->every(fn($a) => $a->justifie),
                        'is_pending'         => $group->contains(fn($a) => !$a->justifie && !empty($a->file_justification)),
                        // ✅ NEW: true when ALL records in this day-group are admin-validated
                        'is_admin_validated' => $group->every(fn($a) => $a->admin_validated),
                        'absences'           => $group,
                    ];
                })
                ->sortByDesc(fn($d) => $d->date?->timestamp)
                ->values();

            $perPage     = 20;
            $currentPage = (int) $request->get('page', 1);
            $absencesGrouped = new \Illuminate\Pagination\LengthAwarePaginator(
                $grouped->forPage($currentPage, $perPage),
                $grouped->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }

        // ── Admin "absents du jour" panel ──────────────────────
        $selectedDay   = null;
        $dayAbsents    = collect();
        $availableDays = collect();
        $prevDay       = null;
        $nextDay       = null;

        if ($canViewAll) {
            $selectedDay = $request->filled('day')
                ? Carbon::parse($request->input('day'))->startOfDay()
                : Carbon::today();

            $dayQuery = AbsenceRetard::with([
                'stagiaire.groupe',
                'cours.emploiDuTemps.module',
                'cours.emploiDuTemps.groupe',
                'cours.emploiDuTemps.salle',
                'cours.emploiDuTemps.gestionnaire',
            ])
            ->where('type', 'absence')
            ->whereDate('date_event', $selectedDay);

            if ($request->filled('groupe_id')) {
                $dayQuery->whereHas('stagiaire', fn($q) => $q->where('id_groupe', $request->groupe_id));
            }
            if ($request->filled('filiere_id') && !$request->filled('groupe_id')) {
                $dayQuery->whereHas('stagiaire.groupe', fn($q) =>
                    $q->where('id_filiere', $request->filiere_id)
                );
            }
            if ($request->filled('stagiaire_id')) {
                $dayQuery->where('id_user', $request->stagiaire_id);
            }

            $dayAbsents = $dayQuery->get()
                ->groupBy('id_user')
                ->map(function ($group) {
                    $first   = $group->first();
                    $emplois = $group->map(fn($a) => $a->cours?->emploiDuTemps)
                        ->filter()->unique('id')->values();

                    return (object) [
                        'stagiaire'          => $first->stagiaire,
                        'total_duree'        => round($group->sum('duree'), 1),
                        'parts'              => $group->pluck('session_part')->filter()->unique()->sort()->values()->toArray(),
                        'emplois'            => $emplois,
                        'modules'            => $emplois->map(fn($e) => $e?->module)->filter()->unique('id')->values(),
                        'formateurs'         => $emplois->map(fn($e) => $e?->gestionnaire)->filter()->unique('id')->values(),
                        'is_justified'       => $group->every(fn($a) => $a->justifie),
                        'is_pending'         => $group->contains(fn($a) => !$a->justifie && !empty($a->file_justification)),
                        // ✅ NEW
                        'is_admin_validated' => $group->every(fn($a) => $a->admin_validated),
                        'absences'           => $group,
                    ];
                })
                ->values()
                ->sortBy('stagiaire.name');

            $availableDays = AbsenceRetard::where('type', 'absence')
                ->selectRaw('DATE(date_event) as day, COUNT(*) as cnt')
                ->groupBy('day')->orderByDesc('day')->limit(60)->get()->keyBy('day');

            $dayStr  = $selectedDay->toDateString();
            $keys    = $availableDays->keys()->sort()->values();
            $idx     = $keys->search($dayStr);
            $prevDay = ($idx !== false && $idx > 0) ? $keys->get($idx - 1)
                     : $keys->first(fn($k) => $k < $dayStr);
            $nextDay = ($idx !== false && $idx < $keys->count() - 1) ? $keys->get($idx + 1)
                     : $keys->first(fn($k) => $k > $dayStr);
        }

        // ── Stagiaire day-grouped view ─────────────────────────
        $absencesByDay = collect();
        if (!$canViewAll) {
            $dayQ = AbsenceRetard::with([
                'cours.emploiDuTemps.module',
                'cours.emploiDuTemps.gestionnaire',
                'cours.emploiDuTemps.salle',
            ])
            ->where('type', 'absence')
            ->where('id_user', $user->id);

            if ($request->filled('justifie') && in_array($request->justifie, ['0', '1', 'pending'])) {
                if ($request->justifie === 'pending') {
                    $dayQ->where('justifie', false)->whereNotNull('file_justification');
                } else {
                    $dayQ->where('justifie', (bool) $request->justifie);
                }
            }
            if ($request->filled('session_part')
                && in_array($request->session_part, ['s1', 's2', 's3', 's4'])) {
                $dayQ->where('session_part', $request->session_part);
            }

            $absencesByDay = $dayQ->orderByDesc('date_event')->get()
                ->groupBy(fn($a) => $a->date_event?->format('Y-m-d') ?? 'x')
                ->map(function ($group) {
                    $first   = $group->first();
                    $emplois = $group->map(fn($a) => $a->cours?->emploiDuTemps)
                        ->filter()->unique('id')->values();

                    return (object) [
                        'date'               => $first->date_event,
                        'total_duree'        => round($group->sum('duree'), 1),
                        'parts'              => $group->pluck('session_part')->filter()->unique()->sort()->values()->toArray(),
                        'modules'            => $emplois->map(fn($e) => $e?->module)->filter()->unique('id')->values(),
                        'emplois'            => $emplois,
                        'formateurs'         => $emplois->map(fn($e) => $e?->gestionnaire)->filter()->unique('id')->values(),
                        'salles'             => $emplois->map(fn($e) => $e?->salle)->filter()->unique('id')->values(),
                        'is_justified'       => $group->every(fn($a) => $a->justifie),
                        'is_pending'         => $group->contains(fn($a) => !$a->justifie && !empty($a->file_justification)),
                        'is_admin_validated' => $group->every(fn($a) => $a->admin_validated),
                        'absences'           => $group,
                    ];
                })
                ->sortByDesc(fn($d) => $d->date?->timestamp)
                ->values();
        }

        return view('absences.index', compact(
            'absences', 'absencesGrouped', 'absencesByDay',
            'stats', 'canViewAll', 'filieres', 'groupes', 'stagiaires', 'filterStagiaire',
            'selectedDay', 'dayAbsents', 'availableDays', 'prevDay', 'nextDay'
        ));
    }

    // ── TOGGLE JUSTIFICATION ──────────────────────────────────
    public function toggleJustification(Request $request, AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $newStatus = !$absence->justifie;
        $absence->update(['justifie' => $newStatus]);

        return back()->with('success',
            'Absence marquée comme ' . ($newStatus ? 'justifiée' : 'non justifiée') . '.'
        );
    }

    // ── ACCEPT JUSTIFICATION ──────────────────────────────────
    public function acceptJustification(AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $absence->update(['justifie' => true]);

        return back()->with('success', 'Justificatif accepté — absence marquée comme justifiée.');
    }

    // ── REJECT JUSTIFICATION ──────────────────────────────────
    public function rejectJustification(AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
            $absence->update([
                'file_justification' => null,
                'justifie'           => false,
            ]);
        }

        return back()->with('success', 'Justificatif rejeté. Le stagiaire peut en soumettre un nouveau.');
    }

    // ══════════════════════════════════════════════════════════
    // ✅ NEW — AUTORISER SANS JUSTIFICATIF
    // Sets admin_validated = true on every absence in the batch.
    // justifie stays FALSE — the absence remains "non justifiée".
    // Effect: the "last-absence" warning shown to formateurs disappears.
    // ══════════════════════════════════════════════════════════
    public function adminValiderSansJustificatif(Request $request)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $ids      = $request->input('absence_ids', []);
        $absences = AbsenceRetard::whereIn('id', $ids)->get();

        if ($absences->isEmpty()) abort(404);

        foreach ($absences as $absence) {
            // Only flip admin_validated — justifie is intentionally left unchanged.
            $absence->update(['admin_validated' => true]);
        }

        return back()->with('success',
            'Absence(s) autorisée(s) sans justificatif. Le signalement formateur est supprimé.'
        );
    }

    // ✅ NEW — ANNULER L'AUTORISATION SANS JUSTIFICATIF
    // Reverts admin_validated to false → warning reappears for formateurs.
    public function adminAnnulerValidation(Request $request)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $ids      = $request->input('absence_ids', []);
        $absences = AbsenceRetard::whereIn('id', $ids)->get();

        if ($absences->isEmpty()) abort(404);

        foreach ($absences as $absence) {
            $absence->update(['admin_validated' => false]);
        }

        return back()->with('success', 'Validation sans justificatif annulée. Le signalement est rétabli.');
    }

    // ── ADMIN UPLOAD FICHIER (direct validation, single record) ─
    public function uploadFichier(Request $request, AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $request->validate([
            'file_justification' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
        }

        $path = $request->file('file_justification')->store('justifications', 'public');

        $absence->update([
            'file_justification' => $path,
            'justifie'           => true,
            'admin_validated'    => false, // properly justified — flag no longer needed
        ]);

        return back()->with('success', 'Fichier de justification uploadé et validé.');
    }

    // ── ADMIN UPLOAD (one file for ALL absences of a day) ─────
    public function adminUploadFichierJour(Request $request)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $ids      = $request->input('absence_ids', []);
        $absences = AbsenceRetard::whereIn('id', $ids)->get();

        if ($absences->isEmpty()) abort(404);

        $request->validate([
            'file_justification' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        foreach ($absences as $absence) {
            if ($absence->file_justification) {
                Storage::disk('public')->delete($absence->file_justification);
            }
        }

        $path = $request->file('file_justification')->store('justifications', 'public');

        foreach ($absences as $absence) {
            $absence->update([
                'file_justification' => $path,
                'justifie'           => true,
                'admin_validated'    => false,
            ]);
        }

        return back()->with('success', 'Justificatif joint et toutes les absences de la journée validées.');
    }

    // ── ADMIN DELETE FICHIER ──────────────────────────────────
    public function deleteFichier(AbsenceRetard $absence)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
            $absence->update([
                'file_justification' => null,
                'justifie'           => false,
            ]);
        }

        return back()->with('success', 'Fichier supprimé.');
    }

    // ── STAGIAIRE UPLOAD (single absence) ────────────────────
    public function stagiaireUploadFichier(Request $request, AbsenceRetard $absence)
    {
        if (Auth::id() !== $absence->id_user) abort(403);
        if ($absence->justifie) return back()->with('error', 'Cette absence est déjà justifiée.');

        $request->validate([
            'file_justification' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
        }

        $path = $request->file('file_justification')->store('justifications', 'public');

        $absence->update([
            'file_justification' => $path,
            'justifie'           => false,
        ]);

        return back()->with('success', 'Justificatif envoyé avec succès. En attente de validation.');
    }

    // ── STAGIAIRE UPLOAD (whole day) ──────────────────────────
    public function stagiaireUploadFichierJour(Request $request)
    {
        $ids      = $request->input('absence_ids', []);
        $absences = AbsenceRetard::whereIn('id', $ids)->where('id_user', Auth::id())->get();

        if ($absences->isEmpty()) abort(403);
        if ($absences->contains(fn($a) => $a->justifie)) {
            return back()->with('error', 'Une ou plusieurs absences sont déjà justifiées.');
        }

        $request->validate([
            'file_justification' => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        foreach ($absences as $absence) {
            if ($absence->file_justification) {
                Storage::disk('public')->delete($absence->file_justification);
            }
        }

        $path = $request->file('file_justification')->store('justifications', 'public');

        foreach ($absences as $absence) {
            $absence->update(['file_justification' => $path, 'justifie' => false]);
        }

        return back()->with('success', 'Justificatif envoyé pour toutes les absences de la journée. En attente de validation.');
    }

    // ── STAGIAIRE DELETE (single absence) ────────────────────
    public function stagiaireDeleteFichier(AbsenceRetard $absence)
    {
        if (Auth::id() !== $absence->id_user) abort(403);
        if ($absence->justifie) return back()->with('error', 'Vous ne pouvez pas supprimer un justificatif déjà accepté.');

        if ($absence->file_justification) {
            Storage::disk('public')->delete($absence->file_justification);
            $absence->update(['file_justification' => null]);
        }

        return back()->with('success', 'Fichier supprimé.');
    }

    // ── STAGIAIRE DELETE (whole day) ──────────────────────────
    public function stagiaireDeleteFichierJour(Request $request)
    {
        $ids      = $request->input('absence_ids', []);
        $absences = AbsenceRetard::whereIn('id', $ids)->where('id_user', Auth::id())->get();

        if ($absences->isEmpty()) abort(403);
        if ($absences->contains(fn($a) => $a->justifie)) {
            return back()->with('error', 'Vous ne pouvez pas supprimer un justificatif déjà accepté.');
        }

        $filePath = $absences->first()?->file_justification;
        if ($filePath) Storage::disk('public')->delete($filePath);

        foreach ($absences as $absence) {
            $absence->update(['file_justification' => null]);
        }

        return back()->with('success', 'Justificatif retiré pour toute la journée.');
    }
}