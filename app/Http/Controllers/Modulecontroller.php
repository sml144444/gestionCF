<?php

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use App\Models\Module;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:groupe-list');
    }

    // ── INDEX ─────────────────────────────────────────────
    public function index(Request $request): View
    {
        $filiereId  = $request->get('filiere');
        $search     = $request->get('search', '');
        $typeFilter = $request->get('type', '');
        $anneeFilter = $request->integer('annee', 0) ?: null; // 1 or 2

        $filieres   = Filiere::orderBy('name')->get();
        $formateurs = User::where('role', 'formateur')->orderBy('name')->get();

        $modules = Module::with(['filiere', 'formateur'])
            ->withCount('emploisDuTemps')
            ->when($filiereId,   fn($q) => $q->where('id_filiere', $filiereId))
            ->when($search,      fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($typeFilter,  fn($q) => $q->where('type', $typeFilter))
            ->when($anneeFilter, fn($q) => $q->where(fn($q2) =>
                // annee matches OR annee is null (shared both years)
                $q2->where('annee', $anneeFilter)->orWhereNull('annee')
            ))
            ->orderBy('id_filiere')
            ->orderBy('annee')
            ->orderBy('name')
            ->get()
            ->groupBy('id_filiere');

        $selectedFiliere = $filiereId ? Filiere::find($filiereId) : null;

        // ── Real progress: hours already done (past sessions) per module ──
        // Sum minutes of all PAST sessions (date_fin <= now) grouped by module
// APRÈS (correct — séparé par année de groupe)
$moduleProgressMap = EmploiDuTemps::whereNotNull('id_module')
    ->whereIn('statut', ['actif', 'brouillon'])
    ->where('emplois_du_temps.date_fin', '<=', Carbon::now())
    ->join('groupes', 'emplois_du_temps.id_groupe', '=', 'groupes.id')
    ->selectRaw('emplois_du_temps.id_module, groupes.annee as groupe_annee,
                 SUM(TIMESTAMPDIFF(MINUTE, emplois_du_temps.date_debut, emplois_du_temps.date_fin)) as total_minutes')
    ->groupBy('emplois_du_temps.id_module', 'groupes.annee')
    ->get()
    ->groupBy('id_module')
    ->map(fn($rows) => $rows->pluck('total_minutes', 'groupe_annee'));
// Résultat : [module_id => [1 => minutes_A1, 2 => minutes_A2]]
        // Stats
        $totalModules  = Module::count();
        $totalHeures   = Module::sum('nbr_heure');
        $totalRegional = Module::where('type', 'regional')->count();
        $totalLocal    = Module::where('type', 'local')->count();

        return view('modules.index', compact(
            'modules', 'filieres', 'formateurs',
            'selectedFiliere', 'search', 'typeFilter', 'anneeFilter',
            'totalModules', 'totalHeures', 'totalRegional', 'totalLocal',
            'moduleProgressMap'
        ));
    }

    // ── STORE ─────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('groupe-create');

        $data = $request->validate([
            'id_filiere'   => 'required|exists:filieres,id',
            'name'         => 'required|string|max:150',
            'coefficience' => 'required|numeric|min:0.5|max:10',
            'nbr_heure'    => 'required|integer|min:1|max:500',
            'id_user'      => 'required|exists:users,id',
            'type'         => 'required|in:regional,local',
            'annee'        => 'nullable|integer|in:1,2',
        ]);

        $exists = Module::where('id_filiere', $data['id_filiere'])
            ->where('name', $data['name'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Un module avec ce nom existe déjà dans cette filière.'])
                ->withInput();
        }

        Module::create($data);

        return back()->with('success', 'Module « ' . $data['name'] . ' » créé avec succès.');
    }

    // ── UPDATE ─────────────────────────────────────────────
    public function update(Request $request, Module $module): RedirectResponse
    {
        $this->authorize('groupe-edit');

        $data = $request->validate([
            'name'         => 'required|string|max:150',
            'coefficience' => 'required|numeric|min:0.5|max:10',
            'nbr_heure'    => 'required|integer|min:1|max:500',
            'id_user'      => 'required|exists:users,id',
            'type'         => 'required|in:regional,local',
            'annee'        => 'nullable|integer|in:1,2',
        ]);

        $exists = Module::where('id_filiere', $module->id_filiere)
            ->where('name', $data['name'])
            ->where('id', '!=', $module->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name_' . $module->id => 'Un module avec ce nom existe déjà dans cette filière.'])
                ->withInput();
        }

        $module->update($data);

        return back()->with('success', 'Module « ' . $module->name . ' » mis à jour.');
    }

    // ── DESTROY ────────────────────────────────────────────
    public function destroy(Module $module): RedirectResponse
    {
        $this->authorize('groupe-delete');

        $emploisCount = $module->emploisDuTemps()->count();
        if ($emploisCount > 0) {
            return back()->with('error',
                'Impossible de supprimer : ce module est utilisé dans ' . $emploisCount . ' séance(s).'
            );
        }

        $name = $module->name;
        $module->delete();

        return back()->with('success', 'Module « ' . $name . ' » supprimé.');
    }
}