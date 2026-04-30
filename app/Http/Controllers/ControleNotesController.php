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
        $promoFilter = $request->get('promo');                       // ← NEW

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

        $filieres = \App\Models\Filiere::orderBy('name')->get();

        // ── Distinct promos from the groupes table ── NEW
        $promos = Groupe::select('promo')
            ->whereNotNull('promo')
            ->distinct()
            ->orderByDesc('promo')
            ->pluck('promo');

        $totalModules = $isFormateur
            ? Module::where('id_user', Auth::id())->count()
            : Module::count();
        $totalHeures = $isFormateur
            ? Module::where('id_user', Auth::id())->sum('nbr_heure')
            : Module::sum('nbr_heure');

        return view('controles.index', compact(
            'modules', 'filieres', 'totalModules', 'totalHeures',
            'search', 'typeFilter', 'filiereId', 'anneeFilter',
            'promos', 'promoFilter'                               // ← NEW
        ));
    }

    // ── MY NOTES (stagiaire view) ─────────────────────────────────────────
    public function myNotes(Request $request): View
    {
        $stagiaire = Auth::user();

        $groupe = \App\Models\Groupe::whereHas(
            'stagiaires',
            fn($q) => $q->where('users.id', $stagiaire->id)
        )->first();

        $modulesWithNotes = collect();
        $generalAverage   = null;

        if ($groupe) {
            $bulletinService = new \App\Services\BulletinService();

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

                $calc = $bulletinService->calculateForModule(
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
                    'moyenne'     => $calc['moduleGrade'],
                ];

                $items[]        = $item;
                $modulesWithNotes->push($item);
            }

            $generalAverage = $bulletinService->calculateGeneralAverage($items);
        }

        return view('controles.my-notes', compact(
            'groupe',
            'modulesWithNotes',
            'generalAverage'
        ));
    }

    // ── NOTES ─────────────────────────────────────────────────────────────
    public function notes(Module $module, Request $request): View
    {
        if (Auth::user()->role === 'formateur' && $module->id_user !== Auth::id()) {
            abort(403, 'Accès refusé : ce module ne vous appartient pas.');
        }

        // ── Promo filter ── NEW
        $promoFilter = $request->get('promo');
        $promos = Groupe::select('promo')
            ->whereNotNull('promo')
            ->distinct()
            ->orderByDesc('promo')
            ->pluck('promo');

        $groupes = Groupe::where('id_filiere', $module->id_filiere)
            ->when($module->annee,  fn($q) => $q->where('annee', $module->annee))
            ->when($promoFilter,    fn($q) => $q->where('promo', $promoFilter))  // ← NEW
            ->orderByDesc('promo')
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
            'stagiaires', 'controles', 'efm', 'notesMap',
            'promos', 'promoFilter'                         // ← NEW
        ));
    }

    // ── UPDATE NBR CONTROLES ──────────────────────────────────────────────
    public function updateNbr(Module $module, Request $request): RedirectResponse
    {
        if (! in_array(Auth::user()->role, ['admin', 'gestionnaire'])) {
            abort(403);
        }

        $data = $request->validate([
            'nbr_controles' => 'required|integer|min:0|max:10',
        ]);

        $module->update(['nbr_controles' => $data['nbr_controles']]);

        $groupeId    = $request->input('groupe_id');
        $promoFilter = $request->input('promo');             // ← NEW

        $params = $groupeId ? ['groupe_id' => $groupeId] : [];
        if ($promoFilter) $params['promo'] = $promoFilter;  // ← NEW

        return redirect()
            ->to(route('controles.notes', array_merge(['module' => $module->id], $params)))
            ->with('success', 'Nombre de contrôles mis à jour.');
    }

    // ── SAVE ──────────────────────────────────────────────────────────────
    public function save(Module $module, Request $request): RedirectResponse
    {
        if (Auth::user()->role === 'formateur' && $module->id_user !== Auth::id()) {
            abort(403);
        }

        $groupeId    = $request->input('groupe_id');
        $promoFilter = $request->input('promo');             // ← NEW
        $notes       = $request->input('notes', []);

        $submittedIds = [];
        foreach ($notes as $stagiaireNotes) {
            foreach (array_keys($stagiaireNotes) as $cid) {
                $submittedIds[] = (int) $cid;
            }
        }
        $submittedIds = array_values(array_unique($submittedIds));

        $efmIds = Controle::whereIn('id', $submittedIds)
            ->where('type', 'efm')
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->toArray();

        foreach ($notes as $stagiaireId => $controleNotes) {
            foreach ($controleNotes as $controleId => $raw) {

                $cId = (int) $controleId;
                $sId = (int) $stagiaireId;

                if ($raw === null || $raw === '') {
                    Note::where('id_user', $sId)
                        ->where('id_controle', $cId)
                        ->delete();
                    continue;
                }

                $input = max(0, (float) $raw);
                $isEfm = in_array($cId, $efmIds);
                $val   = $isEfm
                    ? round(min(20, $input / 2), 2)
                    : round(min(20, $input),     2);

                Note::updateOrCreate(
                    ['id_user' => $sId, 'id_controle' => $cId],
                    ['note'    => $val]
                );
            }
        }

        $params = $groupeId ? ['groupe_id' => $groupeId] : [];
        if ($promoFilter) $params['promo'] = $promoFilter;  // ← NEW

        return redirect()
            ->to(route('controles.notes', array_merge(['module' => $module->id], $params)))
            ->with('success', 'Notes enregistrées avec succès !');
    }
}