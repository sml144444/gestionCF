<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Eff;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Note;
use App\Models\User;
use App\Services\BulletinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BulletinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Toutes les méthodes sauf showSelf sont réservées au staff
        $this->middleware('role:admin,gestionnaire,formateur')
             ->except('showSelf');
    }

    // ── Step 1: choose groupe + stagiaire ────────────────────────
    public function index(Request $request): View
    {
        $filiereFilter = $request->get('filiere_id');
        $promoFilter   = $request->get('promo');

        $filieres = Filiere::orderBy('name')->get();

        $promos = Groupe::select('promo')
            ->whereNotNull('promo')
            ->distinct()
            ->orderByDesc('promo')
            ->pluck('promo');

        $groupes = Groupe::withCount('stagiaires')
            ->when($filiereFilter, fn($q) => $q->where('id_filiere', $filiereFilter))
            ->when($promoFilter,   fn($q) => $q->where('promo', $promoFilter))
            ->orderByDesc('promo')
            ->orderBy('name')
            ->get();

        $selectedGroupe = null;
        $stagiaires     = collect();

        $groupeId = $request->get('groupe_id');

        if ($groupeId) {
            $selectedGroupe = Groupe::findOrFail($groupeId);
            $stagiaires     = $selectedGroupe->stagiaires()->orderBy('name')->get();
        }

        return view('bulletin.index', compact(
            'groupes', 'selectedGroupe', 'stagiaires',
            'filieres', 'promos', 'filiereFilter', 'promoFilter'
        ));
    }

    // ── Step 2: show bulletin for one stagiaire ───────────────────
    public function show(Request $request, User $stagiaire): View
    {
        abort_unless($stagiaire->role === 'stagiaire', 404);

        $groupe = Groupe::with('filiere')->whereHas(
            'stagiaires',
            fn($q) => $q->where('users.id', $stagiaire->id)
        )->first();

        $modulesWithNotes = collect();
        $generalAverage   = null;
        $disciplineNote   = null;
        $isFinalYear      = false;
        $effNote          = null;
        $effRecord        = null;
        $finalGrade       = null;

        if ($groupe) {
            $service = new BulletinService();

            $modules = Module::with(['filiere', 'formateur'])
                ->where('id_filiere', $groupe->id_filiere)
                ->when($groupe->annee, fn($q) => $q->where('annee', $groupe->annee))
                ->orderBy('annee')
                ->orderBy('name')
                ->get();

            $items = [];

            foreach ($modules as $module) {
                $controles = Controle::where('id_module', $module->id)
                    ->where('id_groupe', $groupe->id)
                    ->where('type', 'controle')
                    ->orderBy('variante')
                    ->take(max(0, (int) ($module->nbr_controles ?? 1)))
                    ->get();

                $efm = Controle::where('id_module', $module->id)
                    ->where('id_groupe', $groupe->id)
                    ->where('type', 'efm')
                    ->first();

                $calc = $service->calculateForModule(
                    $controles,
                    $efm,
                    $stagiaire->id
                );

                $allIds = $controles->pluck('id')
                    ->when($efm, fn($c) => $c->push($efm->id))
                    ->filter();

                $notes = Note::where('id_user', $stagiaire->id)
                    ->whereIn('id_controle', $allIds)
                    ->pluck('note', 'id_controle');

                $item = [
                    'module'      => $module,
                    'controles'   => $controles,
                    'efm'         => $efm,
                    'notes'       => $notes,
                    'cc'          => $calc['cc'],
                    'efmDisplay'  => $calc['efmDisplay'],
                    'moduleGrade' => $calc['moduleGrade'],
                ];

                $items[]        = $item;
                $modulesWithNotes->push($item);
            }

            // ── Discipline note ───────────────────────────────────
            $disciplineNote = $service->calculateDisciplineNote($stagiaire->id);

            // ── General average (modules + discipline, weighted) ──
            $generalAverage = $service->calculateGeneralAverage(
                $items,
                $disciplineNote,
                1
            );

            // ── EFF — final year only ─────────────────────────────
            $filiere     = $groupe->filiere;
            $isFinalYear = $filiere && $groupe->annee == $filiere->duree;

            if ($isFinalYear) {
                $effRecord = Eff::where('id_user',    $stagiaire->id)
                               ->where('id_filiere', $groupe->id_filiere)
                               ->first();

                // $effNote is stored as /100 in the database
                $effNote = $effRecord?->note_eff !== null
                    ? (float) $effRecord->note_eff
                    : null;
            }

            // ── Final grade ───────────────────────────────────────
            // Convert EFF from /100 → /20 before passing to service
            $effNoteFor20 = $effNote !== null ? round($effNote / 5, 2) : null;

            $finalGrade = $service->calculateFinalGrade(
                $generalAverage,
                $isFinalYear,
                $effNoteFor20   // pass converted /20 value, not raw /100
            );
        }

        $groupeId      = $request->get('groupe_id', $groupe?->id);
        $filiereFilter = $request->get('filiere_id');
        $promoFilter   = $request->get('promo');

        return view('bulletin.show', compact(
            'stagiaire', 'groupe', 'modulesWithNotes',
            'generalAverage', 'groupeId',
            'filiereFilter', 'promoFilter',
            'disciplineNote',
            'isFinalYear',
            'effNote',      // raw /100 value — for display
            'effRecord',
            'finalGrade'
        ));
    }

    // ── Stagiaire : voir son propre bulletin ──────────────────────
    public function showSelf(Request $request)
    {
        $stagiaire = Auth::user();

        // Sécurité : uniquement les stagiaires
        abort_unless($stagiaire->role === 'stagiaire', 403);

        // Réutilise directement la logique de show()
        // en passant le stagiaire connecté comme paramètre
        return $this->show($request, $stagiaire);
    }

    // ── Store / update EFF note (admin + gestionnaire only) ──────
    public function storeEff(Request $request, User $stagiaire): \Illuminate\Http\RedirectResponse
    {
        abort_unless(
            in_array(Auth::user()->role, ['admin', 'gestionnaire']),
            403
        );
        abort_unless($stagiaire->role === 'stagiaire', 404);

        // EFF is now stored as /100
        $request->validate([
            'eff_note' => 'required|numeric|min:0|max:100',
        ]);

        $groupe = Groupe::with('filiere')->whereHas(
            'stagiaires',
            fn($q) => $q->where('users.id', $stagiaire->id)
        )->first();

        abort_unless($groupe, 404);

        Eff::updateOrCreate(
            [
                'id_user'    => $stagiaire->id,
                'id_filiere' => $groupe->id_filiere,
            ],
            ['note_eff' => $request->eff_note]
        );

        return back()->with('success', 'Note EFF enregistrée avec succès.');
    }
}