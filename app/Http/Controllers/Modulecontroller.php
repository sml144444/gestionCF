<?php

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use App\Models\Module;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
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
        $user        = Auth::user();
        $isFormateur = $user->role === 'formateur';

        $filiereId   = $request->get('filiere');
        $search      = $request->get('search', '');
        $typeFilter  = $request->get('type', '');
        $anneeFilter = $request->integer('annee', 0) ?: null;

        $filieres   = Filiere::orderBy('name')->get();
        $formateurs = User::where('role', 'formateur')->orderBy('name')->get();

        $modules = Module::with(['filiere', 'formateur'])
            ->withCount('emploisDuTemps')
            // ── Formateur scope: only show own modules ──────────
            ->when($isFormateur, fn($q) => $q->where(function ($q2) use ($user) {
                $q2->where('id_user', $user->id)
                   ->orWhere('id_user_remplacant', $user->id);
            }))
            // ────────────────────────────────────────────────────
            ->when($filiereId,   fn($q) => $q->where('id_filiere', $filiereId))
            ->when($search,      fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->when($typeFilter,  fn($q) => $q->where('type', $typeFilter))
            ->when($anneeFilter, fn($q) => $q->where(fn($q2) =>
                $anneeFilter == 3
                    ? $q2->where('annee', 3)->orWhereNull('annee')
                    : $q2->where('annee', $anneeFilter)
            ))
            ->orderBy('id_filiere')
            ->orderBy('annee')
            ->orderBy('name')
            ->get()
            ->groupBy('id_filiere');

        $selectedFiliere = $filiereId ? Filiere::find($filiereId) : null;

        // ── Real progress per module ──────────────────────────
        $moduleProgressMap = EmploiDuTemps::whereNotNull('id_module')
            ->whereIn('statut', ['actif', 'brouillon'])
            ->where('emplois_du_temps.date_fin', '<=', Carbon::now())
            ->join('groupes', 'emplois_du_temps.id_groupe', '=', 'groupes.id')
            ->selectRaw('
                emplois_du_temps.id_module,
                emplois_du_temps.id_groupe,
                groupes.annee   AS groupe_annee,
                groupes.name    AS groupe_name,
                SUM(TIMESTAMPDIFF(MINUTE, emplois_du_temps.date_debut, emplois_du_temps.date_fin)) AS total_minutes
            ')
            ->groupBy(
                'emplois_du_temps.id_module',
                'emplois_du_temps.id_groupe',
                'groupes.annee',
                'groupes.name'
            )
            ->get()
            ->groupBy('id_module');

        // ── Stats (scoped for formateur) ──────────────────────
        $baseQuery     = $isFormateur
            ? Module::where(fn($q) => $q->where('id_user', $user->id)->orWhere('id_user_remplacant', $user->id))
            : Module::query();

        $totalModules  = (clone $baseQuery)->count();
        $totalHeures   = (clone $baseQuery)->sum('nbr_heure');
        $totalRegional = (clone $baseQuery)->where('type', 'regional')->count();
        $totalLocal    = (clone $baseQuery)->where('type', 'local')->count();

        return view('modules.index', compact(
            'modules', 'filieres', 'formateurs',
            'selectedFiliere', 'search', 'typeFilter', 'anneeFilter',
            'totalModules', 'totalHeures', 'totalRegional', 'totalLocal',
            'moduleProgressMap', 'isFormateur'
        ));
    }

    // ── STORE ─────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('groupe-create');

        $data = $request->validate([
            'id_filiere'         => 'required|exists:filieres,id',
            'name'               => 'required|string|max:150',
            'coefficience'       => 'required|numeric|min:0.5|max:10',
            'nbr_heure'          => 'required|integer|min:1|max:500',
            'nbr_controles'      => 'nullable|integer|min:0|max:10',   // ✅ ADDED
            'id_user'            => 'required|exists:users,id',
            'type'               => 'required|in:regional,local',
            'annee'              => 'required|integer|in:1,2,3',
            'id_user_remplacant' => 'nullable|exists:users,id|different:id_user',
        ]);

        $exists = Module::where('id_filiere', $data['id_filiere'])
            ->where('name', $data['name'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Un module avec ce nom existe déjà dans cette filière.'])
                ->withInput();
        }

        // Set default value if not provided
        if (!isset($data['nbr_controles'])) {
            $data['nbr_controles'] = 1;
        }

        Module::create($data);

        return back()->with('success', 'Module « ' . $data['name'] . ' » créé avec succès.');
    }

    // ── UPDATE ─────────────────────────────────────────────
    public function update(Request $request, Module $module): RedirectResponse
    {
        $this->authorize('groupe-edit');

        $data = $request->validate([
            'name'               => 'required|string|max:150',
            'coefficience'       => 'required|numeric|min:0.5|max:10',
            'nbr_heure'          => 'required|integer|min:1|max:500',
            'nbr_controles'      => 'nullable|integer|min:0|max:10',   // ✅ ADDED
            'id_user'            => 'required|exists:users,id',
            'type'               => 'required|in:regional,local',
            'annee'              => 'required|integer|in:1,2,3',
            'id_user_remplacant' => 'nullable|exists:users,id|different:id_user',
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