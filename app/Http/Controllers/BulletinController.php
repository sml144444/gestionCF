<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Note;
use App\Models\User;
use App\Services\BulletinService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BulletinController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,gestionnaire,formateur');
    }

    // ── Step 1: choose groupe + stagiaire ────────────────────────
    public function index(Request $request): View
    {
        $groupes        = Groupe::orderBy('name')->get();
        $selectedGroupe = null;
        $stagiaires     = collect();

        $groupeId = $request->get('groupe_id');

        if ($groupeId) {
            $selectedGroupe = Groupe::findOrFail($groupeId);
            $stagiaires     = $selectedGroupe->stagiaires()->orderBy('name')->get();
        }

        return view('bulletin.index', compact(
            'groupes', 'selectedGroupe', 'stagiaires'
        ));
    }

    // ── Step 2: show bulletin for one stagiaire ───────────────────
    public function show(Request $request, User $stagiaire): View
    {
        abort_unless($stagiaire->role === 'stagiaire', 404);

        $groupe = \App\Models\Groupe::whereHas(
            'stagiaires',
            fn($q) => $q->where('users.id', $stagiaire->id)
        )->first();

        $modulesWithNotes = collect();
        $generalAverage   = null;

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
                    ->take(max(0, (int) ($module->nbr_controles ?? 1)))  // ← ADD THIS
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

                // Raw notes keyed by controle id
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

            $generalAverage = $service->calculateGeneralAverage($items);
        }

        // Pass back groupe_id so the breadcrumb can link back
        $groupeId = $request->get('groupe_id', $groupe?->id);

        return view('bulletin.show', compact(
            'stagiaire', 'groupe', 'modulesWithNotes',
            'generalAverage', 'groupeId'
        ));
    }
}