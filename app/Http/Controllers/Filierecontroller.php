<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // LIST
    // ─────────────────────────────────────────────────────────────────────────

    public function index()
    {
        $filieres = Filiere::withCount(['groupes', 'modules', 'stagiaires'])
            ->with([
                'groupes' => fn ($q) => $q->withCount('stagiaires'),
            ])
            ->get();

        return view('filieres.index', compact('filieres'));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // CREATE
    // ─────────────────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'code'  => 'required|string|max:20|unique:filieres,code',
            'duree' => 'required|integer|in:1,2,3',
        ]);

        Filiere::create($data);

        return back()->with('success', 'Filière créée avec succès.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // UPDATE
    // ─────────────────────────────────────────────────────────────────────────

    public function update(Request $request, Filiere $filiere)
    {
        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'code'  => 'required|string|max:20|unique:filieres,code,' . $filiere->id,
            'duree' => 'required|integer|in:1,2,3',
        ]);

        $filiere->update($data);

        return back()->with('success', 'Filière mise à jour.');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // DELETE
    // ─────────────────────────────────────────────────────────────────────────

    public function destroy(Filiere $filiere)
    {
        if ($filiere->groupes()->count() > 0) {
            return back()->with('error',
                'Impossible de supprimer "' . $filiere->name . '" — ' .
                $filiere->groupes()->count() . ' groupe(s) existants. Supprimez-les d\'abord.'
            );
        }

        $filiere->delete();

        return back()->with('success', 'Filière "' . $filiere->name . '" supprimée.');
    }
}