<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;

class StagiaireController extends Controller
{
    /* ─────────────────────────────────────
     * LIST
     * ───────────────────────────────────── */
    public function index(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('stagiaire-list')) {
            abort(403, 'Accès refusé.');
        }

        $filiereId     = $request->integer('filiere_id', 0) ?: null;
        $groupeId      = $request->integer('groupe_id',  0) ?: null;
        $optionId      = $request->integer('option_id',  0) ?: null;
        $annee         = $request->integer('annee',      0) ?: null;
        $search        = trim($request->input('search',         ''));
        $anneeScolaire = trim($request->input('annee_scolaire', ''));
        $promo         = trim($request->input('promo', ''));

        $hasAnneeScolaireColumn = Schema::hasColumn('groupes', 'annee_scolaire');

        $filieres = Filiere::withCount(['stagiaires'])
            ->with(['groupes' => fn($q) => $q->withCount('stagiaires')])
            ->orderBy('name')
            ->get();

        $totalStagiaires = User::where('role', 'stagiaire')->count();

        $anneesScolaires = collect();
        if ($hasAnneeScolaireColumn) {
            $anneesScolaires = Groupe::select('annee_scolaire')
                ->whereNotNull('annee_scolaire')
                ->where('annee_scolaire', '!=', '')
                ->distinct()
                ->orderByDesc('annee_scolaire')
                ->pluck('annee_scolaire');
        }

        if (! $filiereId) {
            return view('stagiaire.index', compact(
                'filieres', 'totalStagiaires',
                'anneesScolaires', 'hasAnneeScolaireColumn'
            ) + [
                'stagiaires'      => null,
                'groupes'         => collect(),
                'allGroupes'      => collect(),
                'options'         => collect(),
                'selectedFiliere' => null,
                'filiereId'       => null,
                'groupeId'        => null,
                'optionId'        => null,
                'annee'           => null,
                'search'          => '',
                'anneeScolaire'   => '',
                'promo'           => '',
                'promos'          => collect(),
                'hasFilters'      => false,
            ]);
        }

        $selectedFiliere = Filiere::find($filiereId);

        $promos = Groupe::where('id_filiere', $filiereId)
            ->whereNotNull('promo')
            ->where('promo', '!=', '')
            ->orderBy('promo', 'desc')
            ->distinct()
            ->pluck('promo');

        // Filtered groupes used for filter bar
        $groupes = Groupe::with('filiere', 'option')
            ->where('id_filiere', $filiereId)
            ->when($annee, fn($q) => $q->where('annee', $annee))
            ->when($anneeScolaire !== '' && $hasAnneeScolaireColumn,
                fn($q) => $q->where('annee_scolaire', $anneeScolaire))
            ->when($promo, fn($q) => $q->where('promo', $promo))
            ->orderBy('name')
            ->get();

        // All groupes for the filière — used in create/edit modals
        $allGroupes = Groupe::where('id_filiere', $filiereId)
            ->orderBy('annee')
            ->orderBy('name')
            ->get();

        $options = Option::where('id_filiere', $filiereId)->orderBy('titre')->get();

        $stagiaires = User::with('filiere', 'option', 'groupe')
            ->where('role', 'stagiaire')
            ->where('id_filiere', $filiereId)
            ->when($groupeId,  fn($q) => $q->where('id_groupe', $groupeId))
            ->when($optionId,  fn($q) => $q->where('id_option', $optionId))
            ->when($annee,     fn($q) => $q->whereHas('groupe', fn($g) => $g->where('annee', $annee)))
            ->when($anneeScolaire !== '' && $hasAnneeScolaireColumn,
                fn($q) => $q->whereHas('groupe', fn($g) => $g->where('annee_scolaire', $anneeScolaire)))
            ->when($promo,     fn($q) => $q->whereHas('groupe', fn($g) => $g->where('promo', $promo)))
            ->when($search !== '', fn($q) => $q->where(fn($s) =>
                $s->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cin',   'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $hasFilters = $groupeId || $optionId || $annee
                   || $search !== '' || $anneeScolaire !== '' || $promo !== '';

        return view('stagiaire.index', compact(
            'filieres', 'totalStagiaires',
            'selectedFiliere', 'stagiaires', 'groupes', 'allGroupes', 'options',
            'filiereId', 'groupeId', 'optionId', 'annee', 'search',
            'anneeScolaire', 'anneesScolaires',
            'hasAnneeScolaireColumn', 'hasFilters',
            'promo', 'promos'
        ));
    }

    /* ─────────────────────────────────────
     * STORE
     * ───────────────────────────────────── */
    public function store(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('stagiaire-create')) {
            abort(403, 'Accès refusé.');
        }

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'       => ['required', 'string', 'min:6'],
            'cin'            => ['nullable', 'string', 'max:30'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'id_filiere'     => ['required', 'exists:filieres,id'],
            'id_groupe'      => ['nullable', 'exists:groupes,id'],
        ], [
            'name.required'       => 'Le nom est obligatoire.',
            'email.required'      => "L'email est obligatoire.",
            'email.unique'        => 'Cet email est déjà utilisé.',
            'password.required'   => 'Le mot de passe est obligatoire.',
            'password.min'        => 'Minimum 6 caractères.',
            'id_filiere.required' => 'La filière est obligatoire.',
        ]);

        $validated['role']     = 'stagiaire';
        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()
            ->route('stagiaire.index', ['filiere_id' => $validated['id_filiere']])
            ->with('success', 'Stagiaire créé avec succès.');
    }

    /* ─────────────────────────────────────
     * UPDATE
     * ───────────────────────────────────── */
    public function update(Request $request, User $stagiaire)
    {
        if (! auth()->user()->hasPermissionTo('stagiaire-edit')) {
            abort(403, 'Accès refusé.');
        }
        abort_if($stagiaire->role !== 'stagiaire', 403, 'Cible invalide.');

        $validated = $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'email'          => ['required', 'email', 'max:255',
                                 Rule::unique('users', 'email')->ignore($stagiaire->id)],
            'password'       => ['nullable', 'string', 'min:6'],
            'cin'            => ['nullable', 'string', 'max:30'],
            'phone'          => ['nullable', 'string', 'max:20'],
            'date_naissance' => ['nullable', 'date'],
            'id_filiere'     => ['required', 'exists:filieres,id'],
            'id_groupe'      => ['nullable', 'exists:groupes,id'],
            // id_option intentionally removed
        ], [
            'name.required'       => 'Le nom est obligatoire.',
            'email.required'      => "L'email est obligatoire.",
            'email.unique'        => 'Cet email est déjà utilisé.',
            'password.min'        => 'Minimum 6 caractères.',
            'id_filiere.required' => 'La filière est obligatoire.',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        // Ensure empty string is stored as NULL
        $validated['id_groupe'] = $validated['id_groupe'] ?: null;

        $stagiaire->update($validated);

        return redirect()
            ->route('stagiaire.index', ['filiere_id' => $stagiaire->id_filiere])
            ->with('success', 'Stagiaire mis à jour avec succès.');
    }

    /* ─────────────────────────────────────
     * DESTROY
     * ───────────────────────────────────── */
    public function destroy(User $stagiaire)
    {
        if (! auth()->user()->hasPermissionTo('stagiaire-delete')) {
            abort(403, 'Accès refusé.');
        }
        abort_if($stagiaire->role !== 'stagiaire', 403, 'Cible invalide.');

        $filiereId = $stagiaire->id_filiere;
        $stagiaire->delete();

        return redirect()
            ->route('stagiaire.index', ['filiere_id' => $filiereId])
            ->with('success', 'Stagiaire supprimé avec succès.');
    }
}