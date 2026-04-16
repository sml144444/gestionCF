<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    // ── LIST ──────────────────────────────────────────────
    public function index()
    {
        $salles = Salle::withCount([
            'emploisDuTemps as sessions_count',
        ])->orderBy('name')->get();

        return view('salles.index', compact('salles'));
    }

    // ── CREATE ────────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100|unique:salles,name',
            'capacity' => 'required|integer|min:1|max:1000',
        ]);

        Salle::create($validated);

        return back()->with('success', 'Salle « ' . $validated['name'] . ' » créée avec succès.');
    }

    // ── UPDATE ────────────────────────────────────────────
    public function update(Request $request, Salle $salle)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:100|unique:salles,name,' . $salle->id,
            'capacity' => 'required|integer|min:1|max:1000',
        ]);

        $salle->update($validated);

        return back()->with('success', 'Salle mise à jour avec succès.');
    }

    // ── DELETE ────────────────────────────────────────────
    public function destroy(Salle $salle)
    {
        // Prevent deletion if the salle is used in any emploi du temps
        if ($salle->emploisDuTemps()->exists()) {
            return back()->with('error', 'Impossible de supprimer : cette salle est utilisée dans un emploi du temps.');
        }

        $name = $salle->name;
        $salle->delete();

        return back()->with('success', 'Salle « ' . $name . ' » supprimée.');
    }
}