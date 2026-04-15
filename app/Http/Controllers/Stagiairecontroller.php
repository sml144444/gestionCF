<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class StagiaireController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('stagiaire-list')) {
            abort(403, 'Accès refusé.');
        }

        $filiereId     = $request->integer('filiere_id', 0) ?: null;
        $groupeId      = $request->integer('groupe_id',  0) ?: null;
        $optionId      = $request->integer('option_id',  0) ?: null;
        $annee         = $request->integer('annee',      0) ?: null;  // 1 or 2
        $search        = trim($request->input('search',         ''));
        $anneeScolaire = trim($request->input('annee_scolaire', ''));

        $hasAnneeScolaireColumn = Schema::hasColumn('groupes', 'annee_scolaire');

        // ── Filière cards data (always loaded) ──────────────
        $filieres = Filiere::withCount(['stagiaires'])
            ->with(['groupes' => fn($q) => $q->withCount('stagiaires')])
            ->orderBy('name')
            ->get();

        $totalStagiaires = User::where('role', 'stagiaire')->count();

        // ── Academic years dropdown ──────────────────────────
        $anneesScolaires = collect();
        if ($hasAnneeScolaireColumn) {
            $anneesScolaires = Groupe::select('annee_scolaire')
                ->whereNotNull('annee_scolaire')
                ->where('annee_scolaire', '!=', '')
                ->distinct()
                ->orderByDesc('annee_scolaire')
                ->pluck('annee_scolaire');
        }

        // ── If no filière selected → show cards only ────────
        if (! $filiereId) {
            return view('stagiaire.index', compact(
                'filieres', 'totalStagiaires',
                'anneesScolaires', 'hasAnneeScolaireColumn'
            ) + [
                'stagiaires'    => null,
                'groupes'       => collect(),
                'options'       => collect(),
                'selectedFiliere' => null,
                'filiereId'     => null,
                'groupeId'      => null,
                'optionId'      => null,
                'annee'         => null,
                'search'        => '',
                'anneeScolaire' => '',
                'hasFilters'    => false,
            ]);
        }

        // ── Filière selected → load stagiaires ──────────────
        $selectedFiliere = Filiere::find($filiereId);

        $groupes = Groupe::with('filiere', 'option')
            ->where('id_filiere', $filiereId)
            ->when($annee,              fn($q) => $q->where('annee', $annee))
            ->when($anneeScolaire !== '' && $hasAnneeScolaireColumn,
                fn($q) => $q->where('annee_scolaire', $anneeScolaire))
            ->orderBy('name')
            ->get();

        $options = Option::where('id_filiere', $filiereId)->orderBy('titre')->get();

        $stagiaires = User::with('filiere', 'option', 'groupe')
            ->where('role', 'stagiaire')
            ->where('id_filiere', $filiereId)
            ->when($groupeId,  fn($q) => $q->where('id_groupe',  $groupeId))
            ->when($optionId,  fn($q) => $q->where('id_option',  $optionId))
            ->when($annee,     fn($q) => $q->whereHas('groupe', fn($g) => $g->where('annee', $annee)))
            ->when($anneeScolaire !== '' && $hasAnneeScolaireColumn,
                fn($q) => $q->whereHas('groupe', fn($g) => $g->where('annee_scolaire', $anneeScolaire)))
            ->when($search !== '', fn($q) => $q->where(fn($s) =>
                $s->where('name',  'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cin',   'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->paginate(25)
            ->withQueryString();

        $hasFilters = $groupeId || $optionId || $annee
                   || $search !== '' || $anneeScolaire !== '';

        return view('stagiaire.index', compact(
            'filieres', 'totalStagiaires',
            'selectedFiliere', 'stagiaires', 'groupes', 'options',
            'filiereId', 'groupeId', 'optionId', 'annee', 'search',
            'anneeScolaire', 'anneesScolaires',
            'hasAnneeScolaireColumn', 'hasFilters'
        ));
    }
}