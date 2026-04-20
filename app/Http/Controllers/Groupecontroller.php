<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // LIST
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $filiereFilter   = $request->get('filiere');
        $promoFilter     = $request->get('promo');

        $filieres        = Filiere::orderBy('name')->get();
        $selectedFiliere = $filiereFilter ? Filiere::find($filiereFilter) : null;

        // Build query
        $query = Groupe::with('filiere')
            ->withCount('stagiaires'); // ✅ now only counts role=stagiaire (model fixed)

        if ($filiereFilter) {
            $query->where('id_filiere', $filiereFilter);
        }

        if ($promoFilter) {
            $query->where('promo', $promoFilter);
        }

        // Group by filiere for the view
        $groupes = $query->get()->groupBy('id_filiere');

        // Distinct promos for filter dropdown
        $promos = Groupe::whereNotNull('promo')
            ->pluck('promo')
            ->unique()
            ->sort()
            ->values();

        return view('groupes.index', compact(
            'groupes', 'filieres', 'selectedFiliere', 'promos'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'id_filiere' => 'required|exists:filieres,id',
            'name'       => 'required|string|max:100',
            'code'       => 'required|string|max:30|unique:groupes,code',
            'nbr_limit'  => 'required|integer|min:1|max:500',
            'annee'      => 'required|integer|in:1,2,3',
            'promo'      => 'nullable|integer|min:2000|max:2099',
        ]);

        Groupe::create($data);

        return back()->with('success', 'Groupe créé avec succès.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, Groupe $groupe)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'required|string|max:30|unique:groupes,code,' . $groupe->id,
            'nbr_limit' => 'required|integer|min:1|max:500',
            'annee'     => 'required|integer|in:1,2,3',
            'promo'     => 'nullable|integer|min:2000|max:2099',
        ]);

        $groupe->update($data);

        return back()->with('success', 'Groupe "' . $groupe->name . '" modifié.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(Groupe $groupe)
    {
        // ✅ stagiaires() now correctly counts only role=stagiaire
        if ($groupe->stagiaires()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer "' . $groupe->name . '" — ' .
                $groupe->stagiaires()->count() . ' stagiaire(s) dans ce groupe. Désaffectez-les d\'abord.'
            );
        }

        $groupe->delete();

        return back()->with('success', 'Groupe "' . $groupe->name . '" supprimé.');
    }
}