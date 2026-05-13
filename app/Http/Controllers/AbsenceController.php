<?php

namespace App\Http\Controllers;

use App\Models\AbsenceRetard;
use App\Models\User;
use App\Models\Groupe;
use App\Models\Filiere;
use App\Services\NotificationService; // ✅ NEW
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

        // ── Stagiaire day-grouped view (PAGINATED) ─────────────
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

            $grouped = $dayQ->orderByDesc('date_event')->get()
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

            $perPage       = 10;
            $currentPage   = (int) $request->get('page', 1);
            $absencesByDay = new \Illuminate\Pagination\LengthAwarePaginator(
                $grouped->forPage($currentPage, $perPage),
                $grouped->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
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

    // ✅ Only notify once ALL sessions of that day are justified
    $stillUnjustified = AbsenceRetard::where('id_user', $absence->id_user)
        ->where('type', 'absence')
        ->whereDate('date_event', $absence->date_event)
        ->where('justifie', false)
        ->exists();

    if (! $stillUnjustified) {
        $stagiaire = $absence->load('stagiaire', 'cours.emploiDuTemps.module')->stagiaire;
        if ($stagiaire) {
            NotificationService::justificationAcceptee(
                stagiaire:  $stagiaire,
                date:       $absence->date_event?->format('d/m/Y') ?? '—',
                moduleName: $absence->cours?->emploiDuTemps?->module?->name ?? 'N/A',
                url:        route('absences.index', [
                                'day' => $absence->date_event?->toDateString(),
                            ]),
            );
        }
    }

    return back()->with('success', 'Justificatif accepté — absence marquée comme justifiée.');
}

    // ── REJECT JUSTIFICATION ──────────────────────────────────
// ── REJECT JUSTIFICATION ──────────────────────────────────────────────
// ── REJECT JUSTIFICATION ──────────────────────────────────────────────
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

    // ✅ Only notify once no pending sessions remain for that day
    $stillPending = AbsenceRetard::where('id_user', $absence->id_user)
        ->where('type', 'absence')
        ->whereDate('date_event', $absence->date_event)
        ->where('justifie', false)
        ->whereNotNull('file_justification')
        ->exists();

    if (! $stillPending) {
        $stagiaire = $absence->load('stagiaire', 'cours.emploiDuTemps.module')->stagiaire;
        if ($stagiaire) {
            NotificationService::justificationRefusee(
                stagiaire:  $stagiaire,
                date:       $absence->date_event?->format('d/m/Y') ?? '—',
                moduleName: $absence->cours?->emploiDuTemps?->module?->name ?? 'N/A',
                url:        route('absences.index', [
                                'day' => $absence->date_event?->toDateString(),
                            ]),
            );
        }
    }

    return back()->with('success', 'Justificatif rejeté. Le stagiaire peut en soumettre un nouveau.');
}

    // ── AUTORISER SANS JUSTIFICATIF ───────────────────────────
// ── AUTORISER SANS JUSTIFICATIF ───────────────────────────────────────
public function adminValiderSansJustificatif(Request $request)
{
    if (!Auth::user()->can('absence-justify')) abort(403);

    $ids      = $request->input('absence_ids', []);
    $absences = AbsenceRetard::with('stagiaire')->whereIn('id', $ids)->get();

    if ($absences->isEmpty()) abort(404);

    foreach ($absences as $absence) {
        $absence->update(['admin_validated' => true]);
    }

    // ✅ Notify each stagiaire once per group (same day, same user)
    $absences->groupBy('id_user')->each(function ($group) {
        $first     = $group->first();
        $stagiaire = $first->stagiaire;
        if (! $stagiaire) return;

        NotificationService::absenceAutorisee(
            stagiaire: $stagiaire,
            date:      $first->date_event?->format('d/m/Y') ?? '—',
            url:       route('absences.index', [
                           'day' => $first->date_event?->toDateString(),
                       ]),
        );
    });

    return back()->with('success',
        'Absence(s) autorisée(s) sans justificatif. Le signalement formateur est supprimé.'
    );
}

    // ── ANNULER L'AUTORISATION SANS JUSTIFICATIF ─────────────
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
            'admin_validated'    => false,
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

    // ══════════════════════════════════════════════════════════
    // STAGIAIRE UPLOAD (single absence)
    // ✅ Sends notification to admins after file is stored.
    // ══════════════════════════════════════════════════════════
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
    $absence->update(['file_justification' => $path, 'justifie' => false]);

    $moduleName = $absence->load('cours.emploiDuTemps.module')
        ->cours?->emploiDuTemps?->module?->name ?? 'N/A';

    // ✅ Include stagiaire_id + day so the admin lands on the right filtered view
    $date = $absence->date_event?->toDateString();

    NotificationService::justificationSoumise(
        stagiaire:  Auth::user(),
        moduleName: $moduleName,
        url:        route('absences.index', array_filter([
                        'stagiaire_id' => Auth::id(),
                        'day'          => $date,
                    ])),
        absenceIds: [$absence->id],
    );

    return back()->with('success', 'Justificatif envoyé avec succès. En attente de validation.');
}

    // ══════════════════════════════════════════════════════════
    // STAGIAIRE UPLOAD (whole day — multiple absences)
    // ✅ Sends notification to admins after files are stored.
    // ══════════════════════════════════════════════════════════
    public function stagiaireUploadFichierJour(Request $request)
{
    $ids      = $request->input('absence_ids', []);
    $absences = AbsenceRetard::with('cours.emploiDuTemps.module')
        ->whereIn('id', $ids)
        ->where('id_user', Auth::id())
        ->get();

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

    $moduleName = $absences
        ->map(fn($a) => $a->cours?->emploiDuTemps?->module?->name)
        ->filter()
        ->first() ?? 'N/A';

    // ✅ All absences in a "day upload" share the same date
    $date = $absences->first()?->date_event?->toDateString();

    NotificationService::justificationSoumise(
        stagiaire:  Auth::user(),
        moduleName: $moduleName,
        url:        route('absences.index', array_filter([
                        'stagiaire_id' => Auth::id(),
                        'day'          => $date,
                    ])),
        absenceIds: $absences->pluck('id')->toArray(),
    );

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

    // ── BULK JUSTIFY ALL (one day, one student) ───────────────
    public function adminBulkJustify(Request $request)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $ids = $request->input('absence_ids', []);
        AbsenceRetard::whereIn('id', $ids)->update([
            'justifie'        => true,
            'admin_validated' => false,
        ]);

        $count = count($ids);
        return back()->with('success', "$count absence(s) justifiée(s) avec succès.");
    }

    // ── BULK UNJUSTIFY ALL (one day, one student) ────────────
    public function adminBulkUnjustify(Request $request)
    {
        if (!Auth::user()->can('absence-justify')) abort(403);

        $ids = $request->input('absence_ids', []);
        AbsenceRetard::whereIn('id', $ids)->update(['justifie' => false]);

        $count = count($ids);
        return back()->with('success', "Justification annulée pour $count absence(s).");
    }
}