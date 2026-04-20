<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class StagiaireController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // LIST
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $filiereId     = $request->get('filiere_id');
        $search        = $request->get('search', '');
        $groupeId      = $request->get('groupe_id');
        $annee         = $request->get('annee');
        $promo         = $request->get('promo');
        $anneeScolaire = $request->get('annee_scolaire', '');

        $hasAnneeScolaireColumn = Schema::hasColumn('groupes', 'annee_scolaire');

        $filieres = Filiere::withCount('stagiaires')
            ->with(['groupes' => fn ($q) => $q->withCount('stagiaires')])
            ->get();

        $totalStagiaires = User::where('role', 'stagiaire')->count();

        // ── MODE A — no filière selected ────────────────────────────────────
        if (! $filiereId) {
            return view('stagiaire.index', [
                'filiereId'              => null,
                'selectedFiliere'        => null,
                'filieres'               => $filieres,
                'totalStagiaires'        => $totalStagiaires,
                'hasAnneeScolaireColumn' => $hasAnneeScolaireColumn,
                'stagiaires'             => collect(),
                'groupes'                => collect(),
                'allGroupes'             => collect(),
                'search'                 => '',
                'groupeId'               => null,
                'annee'                  => null,
                'promo'                  => null,
                'anneeScolaire'          => '',
                'hasFilters'             => false,
                'anneesScolaires'        => collect(),
                'promos'                 => collect(),
            ]);
        }

        // ── MODE B — filière selected ───────────────────────────────────────
        $selectedFiliere = Filiere::findOrFail($filiereId);

        $groupes = Groupe::where('id_filiere', $filiereId)
            ->withCount('stagiaires')
            ->orderBy('annee')
            ->orderBy('name')
            ->get();

        $allGroupes = $groupes;

        $promos          = $groupes->pluck('promo')->filter()->unique()->sort()->values();
        $anneesScolaires = collect();

        if ($hasAnneeScolaireColumn) {
            $anneesScolaires = Groupe::where('id_filiere', $filiereId)
                ->whereNotNull('annee_scolaire')
                ->pluck('annee_scolaire')
                ->unique()->sort()->values();
        }

        $query = User::where('role', 'stagiaire')
            ->where('id_filiere', $filiereId)
            ->with('groupe')
            ->orderBy('name');

        if ($search) {
            $query->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('cin', 'like', "%{$search}%")
            );
        }

        if ($groupeId)      { $query->where('id_groupe', $groupeId); }
        if ($annee)         { $query->whereHas('groupe', fn ($q) => $q->where('annee', $annee)); }
        if ($promo)         { $query->whereHas('groupe', fn ($q) => $q->where('promo', $promo)); }
        if ($anneeScolaire && $hasAnneeScolaireColumn) {
            $query->whereHas('groupe', fn ($q) => $q->where('annee_scolaire', $anneeScolaire));
        }

        $hasFilters = (bool) ($search || $groupeId || $annee || $promo || $anneeScolaire);
        $stagiaires = $query->paginate(20)->withQueryString();

        return view('stagiaire.index', compact(
            'filiereId', 'selectedFiliere', 'filieres',
            'stagiaires', 'groupes', 'allGroupes',
            'search', 'groupeId', 'annee', 'promo', 'anneeScolaire',
            'hasFilters', 'hasAnneeScolaireColumn', 'anneesScolaires',
            'totalStagiaires', 'promos'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email',
            'password'       => 'required|string|min:6',
            'cin'            => 'nullable|string|max:50',
            'phone'          => 'nullable|string|max:30',
            'date_naissance' => 'nullable|date',
            'id_groupe'      => 'nullable|exists:groupes,id',
            'id_filiere'     => 'required|exists:filieres,id',
        ]);

        if (! empty($data['id_groupe'])) {
            $groupe = Groupe::withCount('stagiaires')->findOrFail($data['id_groupe']);

            // ✅ CAPACITY CHECK
            if ($groupe->stagiaires_count >= $groupe->nbr_limit) {
                return back()->withInput()->withErrors([
                    'id_groupe' => "Le groupe \"{$groupe->name}\" est complet ({$groupe->stagiaires_count}/{$groupe->nbr_limit} places).",
                ]);
            }

            // Sync filiere from the chosen group
            $data['id_filiere'] = $groupe->id_filiere;
        }

        $data['password'] = Hash::make($data['password']);
        $data['role']     = 'stagiaire';

        User::create($data);

        return back()->with('success', 'Stagiaire créé avec succès.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, User $stagiaire)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|unique:users,email,' . $stagiaire->id,
            'password'       => 'nullable|string|min:6',
            'cin'            => 'nullable|string|max:50',
            'phone'          => 'nullable|string|max:30',
            'date_naissance' => 'nullable|date',
            'id_groupe'      => 'nullable|exists:groupes,id',
            'id_filiere'     => 'required|exists:filieres,id',
        ]);

        if (! empty($data['id_groupe'])) {
            $groupe = Groupe::withCount('stagiaires')->findOrFail($data['id_groupe']);

            $isChangingGroup = (string) $stagiaire->id_groupe !== (string) $data['id_groupe'];

            // ✅ CAPACITY CHECK — only when moving to a different group
            if ($isChangingGroup && $groupe->stagiaires_count >= $groupe->nbr_limit) {
                return back()->withInput()->withErrors([
                    'id_groupe' => "Le groupe \"{$groupe->name}\" est complet ({$groupe->stagiaires_count}/{$groupe->nbr_limit} places).",
                ]);
            }

            // Sync filiere from the chosen group
            $data['id_filiere'] = $groupe->id_filiere;
        } else {
            $data['id_groupe'] = null;
        }

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $stagiaire->update($data);

        return back()->with('success', 'Stagiaire "' . $stagiaire->name . '" mis à jour.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(User $stagiaire)
    {
        $name = $stagiaire->name;
        $stagiaire->delete();

        return back()->with('success', 'Stagiaire "' . $name . '" supprimé.');
    }
}