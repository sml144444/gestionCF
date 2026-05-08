<?php

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Salle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EmploiDuTempsController extends Controller
{
    public const SEANCES = [
        1 => ['label' => 'S1', 'start' => '08:30', 'end' => '11:00', 'hours' => 2.5],
        2 => ['label' => 'S2', 'start' => '11:00', 'end' => '13:30', 'hours' => 2.5],
        3 => ['label' => 'S3', 'start' => '13:30', 'end' => '16:00', 'hours' => 2.5],
        4 => ['label' => 'S4', 'start' => '16:00', 'end' => '18:30', 'hours' => 2.5],
    ];

    public const DAYS = [
        1 => 'Lundi', 2 => 'Mardi', 3 => 'Mercredi',
        4 => 'Jeudi', 5 => 'Vendredi', 6 => 'Samedi',
    ];

    // ── INDEX ──────────────────────────────────────────────
    public function index(Request $request)
    {
        $user = auth()->user();

        $defaultYear = 1;
        if ($user->role === 'stagiaire' && $user->id_groupe) {
            $defaultYear = $user->groupe?->annee ?? 1;
        }
        $year = (int) $request->get('year', $defaultYear);

        $currentPromo = Carbon::now()->month >= 9
            ? Carbon::now()->year + 1
            : Carbon::now()->year;
        $promo = (int) $request->get('promo', $currentPromo);

        if (! $user->hasPermissionTo('emploi-view')) {
            abort(403, 'Accès refusé à l\'emploi du temps.');
        }

        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(5)->endOfDay();

        $dayDates = [];
        for ($i = 0; $i < 6; $i++) {
            $dayDates[$i + 1] = $weekStart->copy()->addDays($i);
        }

        $canSeeDraft = $user->hasPermissionTo('emploi-view-all-groups');

        // ── Shared next-week visibility helper ──────────────────
        // FIX: joursAvance = 1 → access opens on Sunday (not Saturday)
        $joursAvance        = 1;
        $prochainLundi      = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeek();
        $visibleDepuis      = $prochainLundi->copy()->subDays($joursAvance); // Sunday 00:00
        $semaineActuelle    = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $estSemaineProchaine = $weekStart->eq($prochainLundi);
        $peutVoirSemaineProchaine = Carbon::now()->gte($visibleDepuis);

        if ($canSeeDraft) {
            // ── Admin / Gestionnaire — no time restriction ──────
            $groupes = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->where('promo', $promo)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();

            $emplois = EmploiDuTemps::with([
                    'module',
                    'groupe.filiere',
                    'salle',
                    'gestionnaire',
                    'remplacant',
                ])
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->whereIn('statut', ['actif', 'brouillon'])
                ->whereIn('id_groupe', $groupes->pluck('id'))
                ->get();

        } elseif ($user->role === 'stagiaire' && $user->id_groupe) {
            // ── Stagiaire — restricted to current week + next week from Sunday ──
            $estSemainePasseeOuActuelle = $weekStart->lte($semaineActuelle);

            if (! $estSemainePasseeOuActuelle && ! ($estSemaineProchaine && $peutVoirSemaineProchaine)) {
                // FIX: blocked — return empty collection, do NOT query DB
                $emplois = collect();
                $groupes = Groupe::with('filiere', 'option')
                    ->where('annee', $year)
                    ->where('promo', $promo)
                    ->where('id', $user->id_groupe)
                    ->get();
            } else {
                $emplois = EmploiDuTemps::with([
                        'module',
                        'groupe.filiere',
                        'salle',
                        'gestionnaire',
                        'remplacant',
                    ])
                    ->whereBetween('date_debut', [$weekStart, $weekEnd])
                    ->where('statut', 'actif')
                    ->where('id_groupe', $user->id_groupe)
                    ->get();

                $groupes = Groupe::with('filiere', 'option')
                    ->where('annee', $year)
                    ->where('promo', $promo)
                    ->where('id', $user->id_groupe)
                    ->get();
            }

        } elseif ($user->role === 'formateur') {
            // ── Formateur — FIX: same Sunday restriction applied server-side ──
            $estSemainePasseeOuActuelle = $weekStart->lte($semaineActuelle);

            if (! $estSemainePasseeOuActuelle && ! ($estSemaineProchaine && $peutVoirSemaineProchaine)) {
                // Blocked — next week not yet visible
                $emplois = collect();
                $groupeIds = collect();
                $groupes   = collect();
            } else {
                $emplois = EmploiDuTemps::with([
                        'module',
                        'groupe.filiere',
                        'salle',
                        'gestionnaire',
                        'remplacant',
                    ])
                    ->whereBetween('date_debut', [$weekStart, $weekEnd])
                    ->where('statut', 'actif')
                    ->where(function ($q) use ($user) {
                        $q->where('id_user', $user->id)
                          ->orWhere('id_user_remplacant', $user->id);
                    })
                    ->get();

                $groupeIds = $emplois->pluck('id_groupe')->unique();
                $groupes   = Groupe::with('filiere', 'option')
                    ->where('annee', $year)
                    ->where('promo', $promo)
                    ->whereIn('id', $groupeIds)
                    ->orderBy('id_filiere')->orderBy('id')
                    ->get();
            }

        } else {
            // ── Other roles — no restriction ──
            $emplois = EmploiDuTemps::with([
                    'module',
                    'groupe.filiere',
                    'salle',
                    'gestionnaire',
                    'remplacant',
                ])
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->where(function ($q) use ($user) {
                    $q->where('id_user', $user->id)
                      ->orWhere('id_user_remplacant', $user->id);
                })
                ->get();

            $groupeIds = $emplois->pluck('id_groupe')->unique();
            $groupes   = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->where('promo', $promo)
                ->whereIn('id', $groupeIds)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();
        }

        // ── Module progress (hours done per group per module) ──
        $moduleProgress = [];
        if ($groupes->isNotEmpty()) {
            $allScheduled = EmploiDuTemps::whereIn('id_groupe', $groupes->pluck('id'))
                ->whereIn('statut', ['actif', 'brouillon'])
                ->whereNotNull('id_module')
                ->where('date_fin', '<=', Carbon::now())
                ->get(['id_groupe', 'id_module', 'date_debut', 'date_fin']);

            foreach ($allScheduled as $e) {
                $gId = $e->id_groupe;
                $mId = $e->id_module;
                if (! isset($moduleProgress[$gId][$mId])) {
                    $moduleProgress[$gId][$mId] = 0.0;
                }
                $moduleProgress[$gId][$mId] += $e->date_debut->diffInMinutes($e->date_fin) / 60;
            }
        }

        $groupesByFiliere = $groupes->groupBy('id_filiere');
        $allGroupes       = Groupe::with('filiere')->where('annee', $year)->where('promo', $promo)->get();
        $salles           = Salle::orderBy('name')->get();
        $formateurs       = User::where('role', 'formateur')->orderBy('name')->get();
        $grid             = $this->buildGrid($groupes, $emplois);

        $draftCount = $canSeeDraft
            ? EmploiDuTemps::whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'brouillon')
                ->whereIn('id_groupe', $groupes->pluck('id'))
                ->count()
            : 0;

        $modulesByFiliereAndAnnee = Module::orderBy('name')->get()
            ->groupBy(fn($m) => $m->id_filiere . '_' . ($m->annee ?? 1))
            ->map(fn($mods) => $mods->map(fn($m) => [
                'id'        => $m->id,
                'name'      => $m->name,
                'nbr_heure' => $m->nbr_heure,
            ])->values());

        $emploisJson = $emplois->map(fn($e) => [
            'id'                 => $e->id,
            'id_groupe'          => $e->id_groupe,
            'id_salle'           => $e->id_salle,
            'id_user'            => $e->id_user,
            'id_user_remplacant' => $e->id_user_remplacant,
            'id_module'          => $e->id_module,
            'date_debut'         => $e->date_debut->format('Y-m-d\TH:i'),
            'date_fin'           => $e->date_fin->format('Y-m-d\TH:i'),
            'mode'               => $e->mode ?? 'presentiel',
            'lien_distance'      => $e->lien_distance ?? '',
            'statut'             => $e->statut,
        ])->values();

        $nextWeekHasSessions = false;
        if ($user->role === 'stagiaire' && $user->id_groupe) {
            $nxtStart            = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeek();
            $nxtEnd              = $nxtStart->copy()->addDays(5)->endOfDay();
            $nextWeekHasSessions = EmploiDuTemps::whereBetween('date_debut', [$nxtStart, $nxtEnd])
                ->where('statut', 'actif')
                ->where('id_groupe', $user->id_groupe)
                ->exists();
        } else {
            $nextWeekHasSessions = true;
        }

        return view('emplois.index', compact(
            'grid', 'year', 'weekStart', 'weekEnd', 'dayDates',
            'groupesByFiliere', 'allGroupes', 'salles', 'formateurs',
            'emplois', 'emploisJson', 'draftCount', 'canSeeDraft',
            'moduleProgress', 'modulesByFiliereAndAnnee',
            'nextWeekHasSessions', 'promo', 'currentPromo'
        ));
    }

    // ── STORE ──────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'id_groupe'     => 'required|exists:groupes,id',
            'id_salle'      => 'nullable|exists:salles,id',
            'id_user'       => 'required|exists:users,id',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after:date_debut',
            'mode'          => 'required|in:presentiel,distance',
            'lien_distance' => 'nullable|string|max:500',
            'id_module'     => 'nullable|exists:modules,id',
        ]);

        if ($user->hasPermissionTo('emploi-view-all-groups') && empty($data['id_module'])) {
            throw ValidationException::withMessages([
                'id_module' => 'Le module est obligatoire pour créer une séance.',
            ]);
        }

        if ($data['mode'] === 'presentiel' && empty($data['id_salle'])) {
            throw ValidationException::withMessages([
                'id_salle' => 'La salle est obligatoire pour une séance en présentiel.',
            ]);
        }

        $debut = Carbon::parse($data['date_debut']);
        $fin   = Carbon::parse($data['date_fin']);

        if ($debut->copy()->startOfDay()->lt(Carbon::today())) {
            throw ValidationException::withMessages([
                'date_debut' => 'Impossible de créer une séance sur une date passée.',
            ]);
        }

        $this->checkOverlaps($debut, $fin, $data, null);

        $idModule = null;
        if ($user->hasPermissionTo('emploi-view-all-groups')) {
            $idModule = $data['id_module'] ?? null;
        }

        $sessionRemplacant = null;
        if ($idModule) {
            $module = Module::find($idModule);
            if ($module && $module->id_user_remplacant) {
                $sessionRemplacant = $module->id_user_remplacant;
            }
        }

        if ($this->tryMergeOnStore($data, $debut, $fin)) {
            $groupe = Groupe::find($data['id_groupe']);
            return redirect()
                ->route('emplois.index', [
                    'week'  => $debut->toDateString(),
                    'year'  => $groupe->annee ?? 1,
                    'promo' => $groupe->promo ?? Carbon::now()->year,
                ])
                ->with('success', 'Séances fusionnées ✓ (brouillon)');
        }

        EmploiDuTemps::create([
            'id_groupe'          => $data['id_groupe'],
            'id_salle'           => $data['mode'] === 'distance' ? null : ($data['id_salle'] ?? null),
            'id_user'            => $data['id_user'],
            'id_user_remplacant' => $sessionRemplacant,
            'id_module'          => $idModule,
            'date_debut'         => $debut,
            'date_fin'           => $fin,
            'jour'               => self::DAYS[$debut->dayOfWeekIso] ?? null,
            'statut'             => 'brouillon',
            'mode'               => $data['mode'],
            'lien_distance'      => $data['mode'] === 'distance' ? ($data['lien_distance'] ?? null) : null,
        ]);

        $groupe = Groupe::find($data['id_groupe']);
        return redirect()
            ->route('emplois.index', [
                'week'  => $debut->toDateString(),
                'year'  => $groupe->annee ?? 1,
                'promo' => $groupe->promo ?? Carbon::now()->year,
            ])
            ->with('success', 'Séance ajoutée en brouillon.');
    }

    // ── PUBLISH ────────────────────────────────────────────
    public function publish(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->hasPermissionTo('emploi-create')) {
            abort(403);
        }

        $year  = (int) $request->get('year', 1);
        $promo = (int) $request->get('promo', Carbon::now()->year);

        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd   = $weekStart->copy()->addDays(5)->endOfDay();
        $groupeIds = Groupe::where('annee', $year)->where('promo', $promo)->pluck('id');

        $published = EmploiDuTemps::whereBetween('date_debut', [$weekStart, $weekEnd])
            ->where('statut', 'brouillon')
            ->whereIn('id_groupe', $groupeIds)
            ->update(['statut' => 'actif']);

        return redirect()
            ->route('emplois.index', [
                'week'  => $weekStart->toDateString(),
                'year'  => $year,
                'promo' => $promo,
            ])
            ->with('success', $published > 0
                ? "{$published} séance(s) publiées ✓"
                : 'Aucune séance en brouillon à publier.');
    }

    // ── UPDATE ──────────────────────────────────────────────
    // PATCHED: id_user_remplacant is intentionally excluded from this method.
    // Replacement is managed exclusively by:
    //   ModuleController::activateReplacement()
    //   ModuleController::deactivateReplacement()
    // Editing a session must never silently wipe replacement history.
    public function update(Request $request, EmploiDuTemps $emploi): RedirectResponse
    {
        $user = auth()->user();

        $data = $request->validate([
            'id_groupe'     => 'required|exists:groupes,id',
            'id_salle'      => 'nullable|exists:salles,id',
            'id_user'       => 'required|exists:users,id',
            'date_debut'    => 'required|date',
            'date_fin'      => 'required|date|after:date_debut',
            'mode'          => 'required|in:presentiel,distance',
            'lien_distance' => 'nullable|string|max:500',
            'id_module'     => 'nullable|exists:modules,id',
            // id_user_remplacant intentionally NOT accepted here.
        ]);

        if ($data['mode'] === 'presentiel' && empty($data['id_salle'])) {
            throw ValidationException::withMessages([
                'id_salle' => 'La salle est obligatoire pour une séance en présentiel.',
            ]);
        }

        $debut = Carbon::parse($data['date_debut']);
        $fin   = Carbon::parse($data['date_fin']);

        $this->checkOverlaps($debut, $fin, $data, $emploi->id);

        $idModule = $emploi->id_module;
        if ($user->hasPermissionTo('emploi-view-all-groups')) {
            $idModule = $data['id_module'] ?? $emploi->id_module;
        }

        $emploi->update([
            'id_groupe'     => $data['id_groupe'],
            'id_salle'      => $data['mode'] === 'distance' ? null : ($data['id_salle'] ?? null),
            'id_user'       => $data['id_user'],
            // ↓ id_user_remplacant deliberately omitted — never overwrite history
            'id_module'     => $idModule,
            'date_debut'    => $debut,
            'date_fin'      => $fin,
            'jour'          => self::DAYS[$debut->dayOfWeekIso] ?? null,
            'mode'          => $data['mode'],
            'lien_distance' => $data['mode'] === 'distance' ? ($data['lien_distance'] ?? null) : null,
        ]);

        $this->tryMergeAdjacent($emploi->fresh());

        $groupe = Groupe::find($data['id_groupe']);
        return redirect()
            ->route('emplois.index', [
                'week'  => $debut->toDateString(),
                'year'  => $groupe->annee ?? 1,
                'promo' => $groupe->promo ?? Carbon::now()->year,
            ])
            ->with('success', 'Séance mise à jour.');
    }

    // ── DESTROY ──────────────────────────────────────────────
    public function destroy(EmploiDuTemps $emploi): RedirectResponse
    {
        $week   = Carbon::parse($emploi->date_debut)->toDateString();
        $groupe = $emploi->groupe;

        $emploi->delete();

        return redirect()
            ->route('emplois.index', [
                'week'  => $week,
                'year'  => $groupe->annee ?? 1,
                'promo' => $groupe->promo ?? Carbon::now()->year,
            ])
            ->with('success', 'Séance supprimée.');
    }

    // ── AVAILABLE RESOURCES (AJAX) ───────────────────────────
    public function available(Request $request): \Illuminate\Http\JsonResponse
    {
        $groupeId    = $request->integer('groupe_id');
        $date        = $request->string('date');
        $seanceStart = $request->integer('seance_start');
        $duration    = $request->integer('duration', 1);
        $excludeId   = $request->integer('exclude_id', 0);
        $mode        = $request->string('mode', 'presentiel');
        $moduleId    = $request->integer('module_id', 0) ?: null;

        $startTime = self::SEANCES[$seanceStart]['start'] ?? '08:30';
        $endSeance = min($seanceStart + $duration - 1, 4);
        $endTime   = self::SEANCES[$endSeance]['end']     ?? '18:30';

        $debut = Carbon::parse($date . ' ' . $startTime);
        $fin   = Carbon::parse($date . ' ' . $endTime);

        $base = EmploiDuTemps::whereIn('statut', ['actif', 'brouillon'])
            ->where('date_debut', '<', $fin)
            ->where('date_fin',   '>', $debut);

        if ($excludeId) {
            $base = (clone $base)->where('id', '!=', $excludeId);
        }

        $busyQuery    = clone $base;
        $busyUserIds  = $busyQuery->get(['id_user', 'id_user_remplacant'])
            ->flatMap(fn($e) => array_filter([$e->id_user, $e->id_user_remplacant]))
            ->unique();
        $busySalleIds = (clone $base)->pluck('id_salle')->unique();

        $groupe = Groupe::find($groupeId);

        if ($moduleId) {
            $module       = Module::find($moduleId);
            $formateurIds = $module && $module->id_user
                ? collect([$module->id_user])
                : collect();
        } else {
            $formateurIds = $groupe
                ? Module::where('id_filiere', $groupe->id_filiere)
                    ->where(function ($q) use ($groupe) {
                        $q->where('annee', $groupe->annee);
                        if ((int) $groupe->annee === 3) {
                            $q->orWhereNull('annee');
                        }
                    })
                    ->whereNotNull('id_user')
                    ->pluck('id_user')
                    ->unique()
                : collect();
        }

        $formateurs = User::where('role', 'formateur')
            ->whereIn('id', $formateurIds)
            ->orderBy('name')
            ->get()
            ->map(fn($f) => [
                'id'        => $f->id,
                'name'      => $f->name,
                'available' => ! $busyUserIds->contains($f->id),
            ]);

        $salles = $mode === 'distance'
            ? collect()
            : Salle::orderBy('name')->get()->map(fn($s) => [
                'id'        => $s->id,
                'name'      => $s->name,
                'capacity'  => $s->capacity,
                'available' => ! $busySalleIds->contains($s->id),
            ]);

        $modules = $groupe
            ? Module::where('id_filiere', $groupe->id_filiere)
                ->where(function ($q) use ($groupe) {
                    $q->where('annee', $groupe->annee);
                    if ((int) $groupe->annee === 3) {
                        $q->orWhereNull('annee');
                    }
                })
                ->orderBy('name')
                ->get()
                ->map(fn($m) => [
                    'id'        => $m->id,
                    'name'      => $m->name,
                    'nbr_heure' => $m->nbr_heure,
                ])
            : collect();

        return response()->json([
            'formateurs' => $formateurs,
            'salles'     => $salles,
            'modules'    => $modules,
            'debut'      => $debut->format('Y-m-d\TH:i'),
            'fin'        => $fin->format('Y-m-d\TH:i'),
        ]);
    }

    // ── UPDATE LIEN ONLY ─────────────────────────────────────
    public function updateLien(Request $request, EmploiDuTemps $emploi): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->hasPermissionTo('emploi-lien')) {
            abort(403);
        }
        if ($user->role === 'formateur' && $emploi->id_user !== $user->id) {
            abort(403);
        }

        $data = $request->validate([
            'lien_distance' => 'nullable|string|max:500',
        ]);

        $emploi->update(['lien_distance' => $data['lien_distance'] ?? null]);

        $groupe = $emploi->groupe;
        return redirect()
            ->route('emplois.index', [
                'week'  => Carbon::parse($emploi->date_debut)->toDateString(),
                'year'  => $groupe->annee ?? 1,
                'promo' => $groupe->promo ?? Carbon::now()->year,
            ])
            ->with('success', 'Lien de réunion mis à jour.');
    }

    // ── DOWNLOAD PDF ─────────────────────────────────────────
    public function downloadPdf(Request $request): \Illuminate\Http\Response
    {
        $user  = auth()->user();
        $year  = (int) $request->get('year', 1);
        $promo = (int) $request->get('promo', Carbon::now()->year);

        if (! $user->hasPermissionTo('emploi-view')) {
            abort(403, 'Accès refusé.');
        }

        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd = $weekStart->copy()->addDays(5)->endOfDay();

        $dayDates = [];
        for ($i = 0; $i < 6; $i++) {
            $dayDates[$i + 1] = $weekStart->copy()->addDays($i);
        }

        $withRelations = [
            'module',
            'groupe.filiere',
            'salle',
            'gestionnaire',
            'remplacant',
        ];

        if ($user->hasPermissionTo('emploi-view-all-groups')) {
            $groupes = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->where('promo', $promo)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();

            $emplois = EmploiDuTemps::with($withRelations)
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->whereIn('id_groupe', $groupes->pluck('id'))
                ->get();

        } elseif ($user->role === 'stagiaire' && $user->id_groupe) {
            $emplois = EmploiDuTemps::with($withRelations)
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->where('id_groupe', $user->id_groupe)
                ->get();

            $groupes = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->where('promo', $promo)
                ->where('id', $user->id_groupe)
                ->get();

        } else {
            $emplois = EmploiDuTemps::with($withRelations)
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->where(function ($q) use ($user) {
                    $q->where('id_user', $user->id)
                      ->orWhere('id_user_remplacant', $user->id);
                })
                ->get();

            $groupeIds = $emplois->pluck('id_groupe')->unique();
            $groupes   = Groupe::with('filiere', 'option')
                ->whereIn('id', $groupeIds)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();
        }

        $groupesByFiliere = $groupes->groupBy('id_filiere');
        $grid             = $this->buildGrid($groupes, $emplois);

        $yearLabel = $user->hasPermissionTo('emploi-view-all-groups')
            ? match($year) {
                1       => '1ère Année',
                2       => '2ème Année',
                3       => '3ème Année',
                default => 'Année ' . $year,
            }
            : 'Toutes les années';

        $filename = 'emploi_semaine_' . $weekStart->format('Y-m-d')
            . ($user->hasPermissionTo('emploi-view-all-groups')
                ? '_annee' . $year . '_promo' . $promo
                : '_formateur_' . $user->id)
            . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emplois.pdf', compact(
            'grid', 'year', 'yearLabel', 'weekStart', 'weekEnd',
            'dayDates', 'groupesByFiliere', 'user', 'promo'
        ))->setPaper('a4', 'landscape');

        return $pdf->download($filename);
    }

    // ── GRID BUILDER ─────────────────────────────────────────
    private function buildGrid($groupes, $emplois): array
    {
        $grid = [];
        foreach ($groupes as $groupe) {
            for ($day = 1; $day <= 6; $day++) {
                for ($seance = 1; $seance <= 4; $seance++) {
                    $grid[$groupe->id][$day][$seance] = ['type' => 'empty'];
                }
            }
        }

        foreach ($emplois as $emploi) {
            $dayNum      = $emploi->date_debut->dayOfWeekIso;
            $startSeance = $this->getStartSeance($emploi->date_debut);
            $colspan     = self::getColspan($emploi->date_debut, $emploi->date_fin);

            if (! isset($grid[$emploi->id_groupe][$dayNum])) continue;

            $grid[$emploi->id_groupe][$dayNum][$startSeance] = [
                'type'        => 'session',
                'emploi'      => $emploi,
                'colspan'     => $colspan,
                'startSeance' => $startSeance,
            ];

            for ($i = 1; $i < $colspan; $i++) {
                $s = $startSeance + $i;
                if ($s <= 4) {
                    $grid[$emploi->id_groupe][$dayNum][$s] = ['type' => 'skip'];
                }
            }
        }

        return $grid;
    }

    // ── HELPERS ──────────────────────────────────────────────
    private function getStartSeance(Carbon $debut): int
    {
        $time = $debut->format('H:i');
        foreach (self::SEANCES as $num => $seance) {
            if ($time === $seance['start']) return $num;
        }
        foreach (self::SEANCES as $num => $seance) {
            $sStart = Carbon::parse($debut->toDateString() . ' ' . $seance['start']);
            $sEnd   = Carbon::parse($debut->toDateString() . ' ' . $seance['end']);
            if ($debut >= $sStart && $debut < $sEnd) return $num;
        }
        return 1;
    }

    public static function getColspan(Carbon $debut, Carbon $fin): int
    {
        $count = 0;
        foreach (self::SEANCES as $seance) {
            $sStart = Carbon::parse($debut->toDateString() . ' ' . $seance['start']);
            $sEnd   = Carbon::parse($debut->toDateString() . ' ' . $seance['end']);
            if ($debut < $sEnd && $fin > $sStart) $count++;
        }
        return max(1, $count);
    }

    public static function totalHours(int $startSeance, int $colspan): float
    {
        $total = 0.0;
        for ($i = 0; $i < $colspan; $i++) {
            $s = $startSeance + $i;
            if (isset(self::SEANCES[$s])) $total += self::SEANCES[$s]['hours'];
        }
        return $total;
    }

    public static function cardColor(?int $moduleId): string
    {
        $colors = ['blue', 'green', 'amber', 'violet', 'red', 'teal', 'slate'];
        return $colors[($moduleId ?? 0) % count($colors)];
    }

    public static function spanLabel(int $startSeance, int $colspan): string
    {
        if ($colspan === 1) return self::SEANCES[$startSeance]['label'] ?? 'S' . $startSeance;
        if ($colspan === 4) return 'Journée';
        $parts = [];
        for ($i = 0; $i < $colspan; $i++) {
            $s       = $startSeance + $i;
            $parts[] = self::SEANCES[$s]['label'] ?? 'S' . $s;
        }
        return implode('+', $parts);
    }

    private function checkOverlaps(Carbon $debut, Carbon $fin, array $data, ?int $excludeId): void
    {
        $base = EmploiDuTemps::whereIn('statut', ['actif', 'brouillon'])
            ->where('date_debut', '<', $fin)
            ->where('date_fin',   '>', $debut);

        if ($excludeId) $base->where('id', '!=', $excludeId);

        if ((clone $base)->where('id_groupe', $data['id_groupe'])->exists()) {
            throw ValidationException::withMessages([
                'id_groupe' => 'Ce groupe a déjà une séance sur ce créneau.',
            ]);
        }
        if (($data['mode'] ?? 'presentiel') === 'presentiel' && ! empty($data['id_salle'])) {
            if ((clone $base)->where('id_salle', $data['id_salle'])->exists()) {
                throw ValidationException::withMessages([
                    'id_salle' => 'Cette salle est déjà occupée sur ce créneau.',
                ]);
            }
        }
        if ((clone $base)->where('id_user', $data['id_user'])->exists()) {
            throw ValidationException::withMessages([
                'id_user' => 'Ce formateur a déjà une séance sur ce créneau.',
            ]);
        }
    }

private function tryMergeOnStore(array $data, Carbon $debut, Carbon $fin): bool
{
    $q = EmploiDuTemps::whereIn('statut', ['actif', 'brouillon'])
        ->where('id_user',   $data['id_user'])
        ->where('id_groupe', $data['id_groupe'])
        ->when(
            is_null($data['id_module'] ?? null),
            fn($q) => $q->whereNull('id_module'),
            fn($q) => $q->where('id_module', $data['id_module'])
        )
        ->whereDate('date_debut', $debut->toDateString());

    $previous = (clone $q)->where('date_fin', $debut)->first();
    if ($previous && $previous->mode === $data['mode']) {
        $previous->update(['date_fin' => $fin]);
        return true;
    }

    $next = (clone $q)->where('date_debut', $fin)->first();
    if ($next && $next->mode === $data['mode']) {
        EmploiDuTemps::create([
            'id_groupe'     => $data['id_groupe'],
            'id_salle'      => $data['id_salle'] ?? null,
            'id_user'       => $data['id_user'],
            'id_module'     => $data['id_module'] ?? null,
            'date_debut'    => $debut,
            'date_fin'      => $next->date_fin,
            'jour'          => self::DAYS[$debut->dayOfWeekIso] ?? null,
            'statut'        => 'brouillon',
            'mode'          => $data['mode'],
            'lien_distance' => $data['lien_distance'] ?? null,
        ]);
        $next->delete();
        return true;
    }

    return false;
}

private function tryMergeAdjacent(EmploiDuTemps $emploi): void
{
    $q = EmploiDuTemps::whereIn('statut', ['actif', 'brouillon'])
        ->where('id', '!=', $emploi->id)
        ->where('id_user',   $emploi->id_user)
        ->where('id_groupe', $emploi->id_groupe)
        ->when(
            is_null($emploi->id_module),
            fn($q) => $q->whereNull('id_module'),
            fn($q) => $q->where('id_module', $emploi->id_module)
        )
        ->whereDate('date_debut', $emploi->date_debut->toDateString());

    $previous = (clone $q)->where('date_fin', $emploi->date_debut)->first();
    if ($previous && $previous->mode === $emploi->mode) {
        $previous->update(['date_fin' => $emploi->date_fin]);
        $emploi->delete();
        return;
    }

    $next = (clone $q)->where('date_debut', $emploi->date_fin)->first();
    if ($next && $next->mode === $emploi->mode) {
        $emploi->update(['date_fin' => $next->date_fin]);
        $next->delete();
    }
}
}