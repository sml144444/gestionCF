<?php

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use App\Models\Module;
use App\Models\ModuleFormateurHistory;
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

        $modules = Module::with(['filiere', 'formateur', 'remplacant'])
            ->withCount('emploisDuTemps')
            ->when($isFormateur, fn($q) => $q->where(function ($q2) use ($user) {
                $q2->where('id_user', $user->id)
                   ->orWhere('id_user_remplacant', $user->id);
            }))
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
            'nbr_controles'      => 'nullable|integer|min:0|max:10',
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

        $data['nbr_controles'] = $data['nbr_controles'] ?? 1;

        $module = Module::create($data);

        // Record initial principal assignment in history
        ModuleFormateurHistory::create([
            'module_id'    => $module->id,
            'formateur_id' => $data['id_user'],
            'type'         => 'principal',
            'start_date'   => now(),
            'end_date'     => null,
            'is_active'    => true,
        ]);

        return back()->with('success', 'Module « ' . $data['name'] . ' » créé avec succès.');
    }

    // ── UPDATE ─────────────────────────────────────────────
    // NOTE: This method handles only name/hours/coeff/type/annee changes.
    // Replacement formateur is managed via activateReplacement() and
    // deactivateReplacement() — NEVER through this method.
    public function update(Request $request, Module $module): RedirectResponse
    {
        $this->authorize('groupe-edit');

        $data = $request->validate([
            'name'          => 'required|string|max:150',
            'coefficience'  => 'required|numeric|min:0.5|max:10',
            'nbr_heure'     => 'required|integer|min:1|max:500',
            'nbr_controles' => 'nullable|integer|min:0|max:10',
            'id_user'       => 'required|exists:users,id',
            'type'          => 'required|in:regional,local',
            'annee'         => 'required|integer|in:1,2,3',
            // id_user_remplacant intentionally EXCLUDED —
            // use activateReplacement() / deactivateReplacement() instead.
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

        // If the principal formateur is being changed, record it in history
        if ((int) $data['id_user'] !== (int) $module->id_user) {
            // Close the previous principal record
            $module->formateurHistory()
                   ->where('type', 'principal')
                   ->where('is_active', true)
                   ->update(['is_active' => false, 'end_date' => now()]);

            // Open a new principal record
            ModuleFormateurHistory::create([
                'module_id'    => $module->id,
                'formateur_id' => $data['id_user'],
                'type'         => 'principal',
                'start_date'   => now(),
                'end_date'     => null,
                'is_active'    => true,
            ]);
        }

        // Update the module but NEVER touch id_user_remplacant here
        $module->update([
            'name'          => $data['name'],
            'coefficience'  => $data['coefficience'],
            'nbr_heure'     => $data['nbr_heure'],
            'nbr_controles' => $data['nbr_controles'] ?? $module->nbr_controles,
            'id_user'       => $data['id_user'],
            'type'          => $data['type'],
            'annee'         => $data['annee'],
            // id_user_remplacant deliberately omitted
        ]);

        return back()->with('success', 'Module « ' . $module->name . ' » mis à jour.');
    }

    // ── ACTIVATE REPLACEMENT ─────────────────────────────
    /**
     * Assign a replacement formateur for a module.
     *
     * - Closes the previous active replacement in history (if any).
     * - Opens a new replacement record in history.
     * - Propagates the replacement to all FUTURE sessions that:
     *     a) belong to this module
     *     b) still have the original formateur (id_user = principal)
     *     c) have no manual session-level override already set
     * - Updates the module's convenience column (id_user_remplacant).
     *
     * Past sessions are NEVER touched.
     */
    public function activateReplacement(Request $request, Module $module): RedirectResponse
    {
        $this->authorize('groupe-edit');

        $data = $request->validate([
            'id_user_remplacant' => [
                'required',
                'exists:users,id',
                'different:' . $module->id_user,
            ],
        ]);

        $newRemplacantId = (int) $data['id_user_remplacant'];
        $now             = now();

        // 1. Close any currently active replacement in history
        $module->formateurHistory()
               ->where('type', 'remplacement')
               ->where('is_active', true)
               ->update(['is_active' => false, 'end_date' => $now]);

        // 2. Record the new replacement
        ModuleFormateurHistory::create([
            'module_id'    => $module->id,
            'formateur_id' => $newRemplacantId,
            'type'         => 'remplacement',
            'start_date'   => $now,
            'end_date'     => null,
            'is_active'    => true,
        ]);

        // 3. Propagate to future sessions only
        //    - Only sessions that still have the original formateur
        //    - Skip sessions that already have a manual session-level override
        EmploiDuTemps::where('id_module', $module->id)
            ->where('id_user', $module->id_user)
            ->whereNull('id_user_remplacant')
            ->where('date_debut', '>', $now)
            ->update(['id_user_remplacant' => $newRemplacantId]);

        // 4. Update the module's convenience column
        $module->update(['id_user_remplacant' => $newRemplacantId]);

        return back()->with('success',
            'Remplacement activé. Les séances futures ont été mises à jour. ' .
            'L\'historique des séances passées est préservé.'
        );
    }

    // ── DEACTIVATE REPLACEMENT ────────────────────────────
    /**
     * Restore the original formateur (remove the active replacement).
     *
     * - Closes the active replacement record in history.
     * - Removes the replacement from FUTURE sessions only.
     * - Past sessions keep their id_user_remplacant — history preserved.
     * - Clears the module's convenience column.
     */
    public function deactivateReplacement(Module $module): RedirectResponse
    {
        $this->authorize('groupe-edit');

        $now = now();

        // 1. Close the active replacement record in history
        $module->formateurHistory()
               ->where('type', 'remplacement')
               ->where('is_active', true)
               ->update(['is_active' => false, 'end_date' => $now]);

        // 2. Remove replacement from future sessions only
        //    Past sessions are untouched — their id_user_remplacant remains
        EmploiDuTemps::where('id_module', $module->id)
            ->where('id_user_remplacant', $module->id_user_remplacant)
            ->where('date_debut', '>', $now)
            ->update(['id_user_remplacant' => null]);

        // 3. Clear the module's convenience column
        $module->update(['id_user_remplacant' => null]);

        return back()->with('success',
            'Formateur original restauré. L\'historique des remplacements passés est conservé.'
        );
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
        $module->delete();  // cascadeOnDelete handles module_formateur_history

        return back()->with('success', 'Module « ' . $name . ' » supprimé.');
    }
}