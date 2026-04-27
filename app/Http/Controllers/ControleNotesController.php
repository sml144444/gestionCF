<?php

namespace App\Http\Controllers;

use App\Models\Controle;
use App\Models\Groupe;
use App\Models\Module;
use App\Models\Note;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ControleNotesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin,gestionnaire,formateur')->except('myNotes');
        $this->middleware('role:admin,gestionnaire,formateur,stagiaire')->only('myNotes');
    }

    // ── INDEX ─────────────────────────────────────────────────────────────
    public function index(Request $request): View
    {
        $search      = $request->get('search', '');
        $typeFilter  = $request->get('type', '');
        $filiereId   = $request->get('filiere');
        $anneeFilter = $request->integer('annee', 0) ?: null;

        $isFormateur = Auth::user()->role === 'formateur';

        $modules = Module::with(['filiere', 'formateur'])
            ->when($isFormateur, fn($q) => $q->where('id_user', Auth::id()))
            ->when($filiereId,   fn($q) => $q->where('id_filiere', $filiereId))
            ->when($search,      fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($typeFilter,  fn($q) => $q->where('type', $typeFilter))
            ->when($anneeFilter, fn($q) => $q->where('annee', $anneeFilter))
            ->orderBy('id_filiere')
            ->orderBy('annee')
            ->orderBy('name')
            ->get()
            ->groupBy('id_filiere');

        $filieres     = \App\Models\Filiere::orderBy('name')->get();

        $totalModules = $isFormateur
            ? Module::where('id_user', Auth::id())->count()
            : Module::count();
        $totalHeures  = $isFormateur
            ? Module::where('id_user', Auth::id())->sum('nbr_heure')
            : Module::sum('nbr_heure');

        return view('controles.index', compact(
            'modules', 'filieres', 'totalModules', 'totalHeures',
            'search', 'typeFilter', 'filiereId', 'anneeFilter'
        ));
    }

    // ── MY NOTES (stagiaire view) ─────────────────────────────────────────
    public function myNotes(Request $request): View
    {
        $stagiaire = Auth::user();

        // Find the groupe this stagiaire belongs to
        $groupe = \App\Models\Groupe::whereHas('stagiaires', fn($q) => $q->where('users.id', $stagiaire->id))
            ->first();

        $modulesWithNotes = collect();

        if ($groupe) {
            // Get all modules for this groupe's filière and annee
            $modules = Module::with(['filiere', 'formateur'])
                ->where('id_filiere', $groupe->id_filiere)
                ->when($groupe->annee, fn($q) => $q->where('annee', $groupe->annee))
                ->orderBy('annee')
                ->orderBy('name')
                ->get();

            foreach ($modules as $module) {
                // Get controles for this module + groupe
                $controles = Controle::where('id_module', $module->id)
                    ->where('id_groupe', $groupe->id)
                    ->where('type', 'controle')
                    ->orderBy('variante')
                    ->get();

                $efm = Controle::where('id_module', $module->id)
                    ->where('id_groupe', $groupe->id)
                    ->where('type', 'efm')
                    ->first();

                // Get this stagiaire's notes
                $allControleIds = $controles->pluck('id')
                    ->when($efm, fn($c) => $c->push($efm->id))
                    ->filter();

                $notes = Note::where('id_user', $stagiaire->id)
                    ->whereIn('id_controle', $allControleIds)
                    ->pluck('note', 'id_controle');

                // Calculate moyenne (all notes normalised to /20)
                $noteValues = [];
                foreach ($controles as $ctrl) {
                    $val = $notes[$ctrl->id] ?? null;
                    if ($val !== null) $noteValues[] = (float) $val; // already /20
                }
                if ($efm) {
                    $efmRaw = $notes[$efm->id] ?? null;
                    if ($efmRaw !== null) $noteValues[] = (float) $efmRaw; // stored /20
                }

                $moyenne = count($noteValues)
                    ? round(array_sum($noteValues) / count($noteValues), 2)
                    : null;

                $modulesWithNotes->push([
                    'module'    => $module,
                    'controles' => $controles,
                    'efm'       => $efm,
                    'notes'     => $notes,
                    'moyenne'   => $moyenne,
                ]);
            }
        }

        return view('controles.my-notes', compact('groupe', 'modulesWithNotes'));
    }

    // ── NOTES ─────────────────────────────────────────────────────────────
    public function notes(Module $module, Request $request): View
    {
        if (Auth::user()->role === 'formateur' && $module->id_user !== Auth::id()) {
            abort(403, 'Accès refusé : ce module ne vous appartient pas.');
        }

        $groupes = Groupe::where('id_filiere', $module->id_filiere)
            ->when($module->annee, fn($q) => $q->where('annee', $module->annee))
            ->orderBy('name')
            ->get();

        $groupeId       = $request->get('groupe_id');
        $selectedGroupe = null;
        $stagiaires     = collect();
        $controles      = collect();
        $efm            = null;
        $notesMap       = [];

        if ($groupeId) {
            $selectedGroupe = Groupe::findOrFail($groupeId);
            $stagiaires     = $selectedGroupe->stagiaires()->orderBy('name')->get();

            $nbr = max(0, (int) ($module->nbr_controles ?? 1));

            for ($i = 1; $i <= $nbr; $i++) {
                $c = Controle::where('id_module', $module->id)
                             ->where('id_groupe', $selectedGroupe->id)
                             ->where('type', 'controle')
                             ->where('variante', (string) $i)
                             ->first();

                if (! $c) {
                    $c = Controle::create([
                        'titre'      => 'Contrôle ' . $i,
                        'id_module'  => $module->id,
                        'id_groupe'  => $selectedGroupe->id,
                        'type'       => 'controle',
                        'variante'   => (string) $i,
                        'created_by' => Auth::id(),
                        'date'       => now()->toDateString(),
                    ]);
                }

                $controles->push($c);
            }

            $efm = Controle::where('id_module', $module->id)
                           ->where('id_groupe', $selectedGroupe->id)
                           ->where('type', 'efm')
                           ->first();

            if (! $efm) {
                $efm = Controle::create([
                    'titre'      => 'EFM',
                    'id_module'  => $module->id,
                    'id_groupe'  => $selectedGroupe->id,
                    'type'       => 'efm',
                    'variante'   => '0',
                    'created_by' => Auth::id(),
                    'date'       => now()->toDateString(),
                ]);
            }

            $allIds = $controles->pluck('id')->push($efm->id)->filter()->values();

            Note::whereIn('id_controle', $allIds->all())
                ->get()
                ->each(function ($n) use (&$notesMap) {
                    $notesMap[(int) $n->id_user][(int) $n->id_controle] = $n->note;
                });
        }

        return view('controles.notes', compact(
            'module', 'groupes', 'selectedGroupe',
            'stagiaires', 'controles', 'efm', 'notesMap'
        ));
    }

    // ── UPDATE NBR CONTROLES ───────────────────────────────────────────────
    public function updateNbr(Module $module, Request $request): RedirectResponse
    {
        // Only admin and gestionnaire can update nbr_controles
        if (! in_array(Auth::user()->role, ['admin', 'gestionnaire'])) {
            abort(403);
        }

        $data = $request->validate([
            'nbr_controles' => 'required|integer|min:0|max:10',
        ]);

        $module->update(['nbr_controles' => $data['nbr_controles']]);

        $groupeId = $request->input('groupe_id');

        return redirect()
            ->to(route('controles.notes', $module->id) . ($groupeId ? '?groupe_id=' . $groupeId : ''))
            ->with('success', 'Nombre de contrôles mis à jour.');
    }

    // ── SAVE ──────────────────────────────────────────────────────────────
    public function save(Module $module, Request $request): RedirectResponse
    {
        if (Auth::user()->role === 'formateur' && $module->id_user !== Auth::id()) {
            abort(403);
        }

        $groupeId = $request->input('groupe_id');
        $notes    = $request->input('notes', []);

        $allControleIds = collect($notes)
            ->flatMap(fn($row) => array_keys($row))
            ->unique()
            ->values();

        $controleTypes = Controle::whereIn('id', $allControleIds)
            ->pluck('type', 'id');

        foreach ($notes as $stagiaireId => $controleNotes) {
            foreach ($controleNotes as $controleId => $raw) {
                $isEmpty = ($raw === null || $raw === '');

                if ($isEmpty) {
                    Note::where('id_user',     $stagiaireId)
                        ->where('id_controle', $controleId)
                        ->delete();
                } else {
                    $isEfm = ($controleTypes[$controleId] ?? 'controle') === 'efm';
                    $input = max(0, (float) $raw);

                    $val = $isEfm
                        ? round(min(20, $input / 2), 2)
                        : round(min(20, $input),     2);

                    Note::updateOrCreate(
                        ['id_user'     => $stagiaireId,
                         'id_controle' => $controleId],
                        ['note'        => $val]
                    );
                }
            }
        }

        return redirect()
            ->to(route('controles.notes', $module->id) . ($groupeId ? '?groupe_id=' . $groupeId : ''))
            ->with('success', 'Notes enregistrées avec succès !');
    }
}