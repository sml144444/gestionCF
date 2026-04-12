<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Option;
use App\Models\User;
use Illuminate\Http\Request;

class StagiaireController extends Controller
{
    public function index(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('stagiaire-list')) {
            abort(403, 'Accès refusé.');
        }

        // ── Filters from query string
        $filiereId = $request->integer('filiere_id', 0) ?: null;
        $groupeId  = $request->integer('groupe_id',  0) ?: null;
        $optionId  = $request->integer('option_id',  0) ?: null;
        $search    = $request->string('search', '')->trim();
        $annee     = $request->integer('annee', 0) ?: null;

        // ── Reference data for filters
        $filieres = Filiere::orderBy('name')->get();

        $groupes = Groupe::with('filiere', 'option')
            ->when($filiereId, fn($q) => $q->where('id_filiere', $filiereId))
            ->when($annee,     fn($q) => $q->where('annee', $annee))
            ->orderBy('name')
            ->get();

        $options = Option::when($filiereId, fn($q) => $q->where('id_filiere', $filiereId))
            ->orderBy('titre')
            ->get();

        // ── Stagiaire query
        $stagiaires = User::with('filiere', 'option', 'groupe')
            ->where('role', 'stagiaire')
            ->when($filiereId, fn($q) => $q->where('id_filiere', $filiereId))
            ->when($groupeId,  fn($q) => $q->where('id_groupe',  $groupeId))
            ->when($optionId,  fn($q) => $q->where('id_option',  $optionId))
            ->when($annee, fn($q) => $q->whereHas('groupe', fn($g) => $g->where('annee', $annee)))
            ->when($search, fn($q) => $q->where(fn($s) =>
                $s->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('cin',   'like', "%{$search}%")
            ))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        // ── Stats
        $totalStagiaires = User::where('role', 'stagiaire')->count();

        $statsByFiliere = Filiere::withCount([
            'stagiaires' => fn($q) => $q->where('role', 'stagiaire'),
        ])->orderBy('name')->get();

        return view('stagiaire.index', compact(
            'stagiaires', 'filieres', 'groupes', 'options',
            'filiereId', 'groupeId', 'optionId', 'search', 'annee',
            'totalStagiaires', 'statsByFiliere'
        ));
    }
}