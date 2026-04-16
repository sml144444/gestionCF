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
        $year = (int) $request->get('year', 1);

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

        // ── Data filtering based on permissions ──────────────
        if ($canSeeDraft) {
            $groupes = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();

            $emplois = EmploiDuTemps::with(['module', 'groupe.filiere', 'salle', 'gestionnaire'])
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->whereIn('statut', ['actif', 'brouillon'])
                ->whereIn('id_groupe', $groupes->pluck('id'))
                ->get();

        } elseif ($user->role === 'stagiaire' && $user->id_groupe) {

            $joursAvance   = 2;
            $prochainLundi = Carbon::now()->startOfWeek(Carbon::MONDAY)->addWeek();
            $visibleDepuis = $prochainLundi->copy()->subDays($joursAvance);

            $semaineActuelle              = Carbon::now()->startOfWeek(Carbon::MONDAY);
            $estSemainePasseeOuActuelle   = $weekStart->lte($semaineActuelle);
            $estSemaineProchaine          = $weekStart->eq($prochainLundi);
            $peutVoirSemaineProchaine     = Carbon::now()->gte($visibleDepuis);

            if (! $estSemainePasseeOuActuelle && ! ($estSemaineProchaine && $peutVoirSemaineProchaine)) {
                $emplois = collect();
                $groupes = Groupe::with('filiere', 'option')
                    ->where('annee', $year)
                    ->where('id', $user->id_groupe)
                    ->get();
            } else {
                $emplois = EmploiDuTemps::with(['module', 'groupe.filiere', 'salle', 'gestionnaire'])
                    ->whereBetween('date_debut', [$weekStart, $weekEnd])
                    ->where('statut', 'actif')
                    ->where('id_groupe', $user->id_groupe)
                    ->get();

                $groupes = Groupe::with('filiere', 'option')
                    ->where('annee', $year)
                    ->where('id', $user->id_groupe)
                    ->get();
            }

        } else {
            // Formateur or restricted user — only their own sessions
            $emplois = EmploiDuTemps::with(['module', 'groupe.filiere', 'salle', 'gestionnaire'])
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->where('id_user', $user->id)
                ->get();

            $groupeIds = $emplois->pluck('id_groupe')->unique();
            $groupes   = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->whereIn('id', $groupeIds)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();
        }

        // ── Module progress (dynamic, no DB storage) ─────────
        // For each groupe+module pair, sum all scheduled hours (all time, not just this week)
        $moduleProgress = [];
        if ($groupes->isNotEmpty()) {
            $allScheduled = EmploiDuTemps::whereIn('id_groupe', $groupes->pluck('id'))
                ->whereIn('statut', ['actif', 'brouillon'])
                ->whereNotNull('id_module')
                ->where('date_fin', '<=', Carbon::now()) // ← only sessions already finished
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
        $allGroupes       = Groupe::with('filiere')->where('annee', $year)->get();
        $salles           = Salle::orderBy('name')->get();
        $formateurs       = User::where('role', 'formateur')->orderBy('name')->get();
        $grid             = $this->buildGrid($groupes, $emplois);

        $draftCount = $canSeeDraft
            ? EmploiDuTemps::whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'brouillon')
                ->whereIn('id_groupe', $groupes->pluck('id'))
                ->count()
            : 0;

        // Modules grouped by filiere_id for JS (used in modal select)
$modulesByFiliereAndAnnee = Module::orderBy('name')->get()
    ->groupBy(fn($m) => $m->id_filiere . '_' . ($m->annee ?? 1))
    ->map(fn($mods) => $mods->map(fn($m) => [
        'id'        => $m->id,
        'name'      => $m->name,
        'nbr_heure' => $m->nbr_heure,
    ])->values());

        $emploisJson = $emplois->map(fn($e) => [
            'id'            => $e->id,
            'id_groupe'     => $e->id_groupe,
            'id_salle'      => $e->id_salle,
            'id_user'       => $e->id_user,
            'id_module'     => $e->id_module,
            'date_debut'    => $e->date_debut->format('Y-m-d\TH:i'),
            'date_fin'      => $e->date_fin->format('Y-m-d\TH:i'),
            'mode'          => $e->mode ?? 'presentiel',
            'lien_distance' => $e->lien_distance ?? '',
            'statut'        => $e->statut,
        ])->values();

        return view('emplois.index', compact(
            'grid', 'year', 'weekStart', 'weekEnd', 'dayDates',
            'groupesByFiliere', 'allGroupes', 'salles', 'formateurs',
            'emplois', 'emploisJson', 'draftCount', 'canSeeDraft',
            'moduleProgress', 'modulesByFiliereAndAnnee' 
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

        // Gestionnaire/admin : module obligatoire
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

        $this->checkOverlaps($debut, $fin, $data, null);

        // Determine module to save
        $idModule = null;
        if ($user->hasPermissionTo('emploi-view-all-groups') || $user->hasPermissionTo('emploi-change-module')) {
            $idModule = $data['id_module'] ?? null;
        }

        if ($this->tryMergeOnStore($data, $debut, $fin)) {
            $year = Groupe::find($data['id_groupe'])->annee ?? 1;
            return redirect()
                ->route('emplois.index', ['week' => $debut->toDateString(), 'year' => $year])
                ->with('success', 'Séances fusionnées ✓ (brouillon)');
        }

        EmploiDuTemps::create([
            'id_groupe'     => $data['id_groupe'],
            'id_salle'      => $data['mode'] === 'distance' ? null : ($data['id_salle'] ?? null),
            'id_user'       => $data['id_user'],
            'id_module'     => $idModule,
            'date_debut'    => $debut,
            'date_fin'      => $fin,
            'jour'          => self::DAYS[$debut->dayOfWeekIso] ?? null,
            'statut'        => 'brouillon',
            'mode'          => $data['mode'],
            'lien_distance' => $data['mode'] === 'distance' ? ($data['lien_distance'] ?? null) : null,
        ]);

        $year = Groupe::find($data['id_groupe'])->annee ?? 1;
        return redirect()
            ->route('emplois.index', ['week' => $debut->toDateString(), 'year' => $year])
            ->with('success', 'Séance ajoutée en brouillon. Cliquez « Publier » pour la rendre visible.');
    }

    // ── PUBLISH ────────────────────────────────────────────
    public function publish(Request $request): RedirectResponse
    {
        $user = auth()->user();

        if (! $user->hasPermissionTo('emploi-create')) {
            abort(403);
        }

        $year  = (int) $request->get('year', 1);

        $weekStart = $request->has('week')
            ? Carbon::parse($request->week)->startOfWeek(Carbon::MONDAY)
            : Carbon::now()->startOfWeek(Carbon::MONDAY);

        $weekEnd   = $weekStart->copy()->addDays(5)->endOfDay();
        $groupeIds = Groupe::where('annee', $year)->pluck('id');

        $published = EmploiDuTemps::whereBetween('date_debut', [$weekStart, $weekEnd])
            ->where('statut', 'brouillon')
            ->whereIn('id_groupe', $groupeIds)
            ->update(['statut' => 'actif']);

        return redirect()
            ->route('emplois.index', ['week' => $weekStart->toDateString(), 'year' => $year])
            ->with('success', $published > 0
                ? "{$published} séance(s) publiées — maintenant visibles pour tous ✓"
                : 'Aucune séance en brouillon à publier.');
    }

    // ── UPDATE ──────────────────────────────────────────────
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
        ]);

        if ($data['mode'] === 'presentiel' && empty($data['id_salle'])) {
            throw ValidationException::withMessages([
                'id_salle' => 'La salle est obligatoire pour une séance en présentiel.',
            ]);
        }

        $debut = Carbon::parse($data['date_debut']);
        $fin   = Carbon::parse($data['date_fin']);

        $this->checkOverlaps($debut, $fin, $data, $emploi->id);

        // Determine which module value to persist
        $idModule = $emploi->id_module; // keep existing by default
        if ($user->hasPermissionTo('emploi-view-all-groups') || $user->hasPermissionTo('emploi-change-module')) {
            $idModule = $data['id_module'] ?? $emploi->id_module;
        }

        $emploi->update([
            'id_groupe'     => $data['id_groupe'],
            'id_salle'      => $data['mode'] === 'distance' ? null : ($data['id_salle'] ?? null),
            'id_user'       => $data['id_user'],
            'id_module'     => $idModule,
            'date_debut'    => $debut,
            'date_fin'      => $fin,
            'jour'          => self::DAYS[$debut->dayOfWeekIso] ?? null,
            'mode'          => $data['mode'],
            'lien_distance' => $data['mode'] === 'distance' ? ($data['lien_distance'] ?? null) : null,
        ]);

        $this->tryMergeAdjacent($emploi->fresh());

        $year = Groupe::find($data['id_groupe'])->annee ?? 1;
        return redirect()
            ->route('emplois.index', ['week' => $debut->toDateString(), 'year' => $year])
            ->with('success', 'Séance mise à jour.');
    }

    // ── DESTROY ──────────────────────────────────────────────
    public function destroy(EmploiDuTemps $emploi): RedirectResponse
    {
        $week = Carbon::parse($emploi->date_debut)->toDateString();
        $year = $emploi->groupe->annee ?? 1;
        $emploi->delete();

        return redirect()
            ->route('emplois.index', ['week' => $week, 'year' => $year])
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

        $busyUserIds  = (clone $base)->pluck('id_user')->unique();
        $busySalleIds = (clone $base)->pluck('id_salle')->unique();

        $formateurs = User::where('role', 'formateur')
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

        // ── Modules filtered by the groupe's filiere ─────────
        $groupe  = Groupe::find($groupeId);
$modules = $groupe
    ? Module::where('id_filiere', $groupe->id_filiere)
        ->where('annee', $groupe->annee)          // ← add this line
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

    // ── UPDATE LIEN ONLY (formateur) ─────────────────────────
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

        return redirect()
            ->route('emplois.index', [
                'week' => Carbon::parse($emploi->date_debut)->toDateString(),
                'year' => $emploi->groupe->annee ?? 1,
            ])
            ->with('success', 'Lien de réunion mis à jour.');
    }

    // ── DOWNLOAD PDF ─────────────────────────────────────────
    public function downloadPdf(Request $request): \Illuminate\Http\Response
    {
        $user = auth()->user();
        $year = (int) $request->get('year', 1);

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

        if ($user->hasPermissionTo('emploi-view-all-groups')) {
            $groupes = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();

            $emplois = EmploiDuTemps::with(['module', 'groupe.filiere', 'salle', 'gestionnaire'])
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->whereIn('id_groupe', $groupes->pluck('id'))
                ->get();

        } elseif ($user->role === 'stagiaire' && $user->id_groupe) {
            $emplois = EmploiDuTemps::with(['module', 'groupe.filiere', 'salle', 'gestionnaire'])
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->where('id_groupe', $user->id_groupe)
                ->get();

            $groupes = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->where('id', $user->id_groupe)
                ->get();

        } else {
            $emplois = EmploiDuTemps::with(['module', 'groupe.filiere', 'salle', 'gestionnaire'])
                ->whereBetween('date_debut', [$weekStart, $weekEnd])
                ->where('statut', 'actif')
                ->where('id_user', $user->id)
                ->get();

            $groupeIds = $emplois->pluck('id_groupe')->unique();
            $groupes   = Groupe::with('filiere', 'option')
                ->where('annee', $year)
                ->whereIn('id', $groupeIds)
                ->orderBy('id_filiere')->orderBy('id')
                ->get();
        }

        $groupesByFiliere = $groupes->groupBy('id_filiere');
        $grid             = $this->buildGrid($groupes, $emplois);

        $yearLabel = match($year) {
    1 => '1ère Année',
    2 => '2ème Année',
    3 => '3ème Année',
    default => 'Année ' . $year,
};
        $filename  = 'emploi_semaine_' . $weekStart->format('Y-m-d') . '_annee' . $year . '.pdf';

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('emplois.pdf', compact(
            'grid', 'year', 'yearLabel', 'weekStart', 'weekEnd',
            'dayDates', 'groupesByFiliere', 'user'
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