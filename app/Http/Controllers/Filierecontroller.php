<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FiliereController extends Controller
{
    // ── INDEX ─────────────────────────────────────────────
    public function index()
    {
        $user        = Auth::user();
        $isFormateur = $user->role === 'formateur';

        $filieres = Filiere::withCount(['groupes', 'modules', 'stagiaires'])
            ->with(['groupes' => fn($q) => $q->withCount('stagiaires')])
            // ── Formateur sees only filieres of their modules ──
            ->when($isFormateur, fn($q) => $q->whereHas(
                'modules', fn($mq) =>
                    $mq->where('id_user', $user->id)
                       ->orWhere('id_user_remplacant', $user->id)
            ))
            ->get();

        return view('filieres.index', compact('filieres', 'isFormateur'));
    }

    // ── STORE ─────────────────────────────────────────────
    public function store(Request $request)
    {
        $this->authorize('groupe-create');

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'code'  => 'required|string|max:20|unique:filieres,code',
            'duree' => 'required|integer|in:1,2,3',
        ]);

        Filiere::create($data);

        return back()->with('success', 'Filière créée avec succès.');
    }

    // ── UPDATE ─────────────────────────────────────────────
    public function update(Request $request, Filiere $filiere)
    {
        $this->authorize('groupe-edit');

        $data = $request->validate([
            'name'  => 'required|string|max:255',
            'code'  => 'required|string|max:20|unique:filieres,code,' . $filiere->id,
            'duree' => 'required|integer|in:1,2,3',
        ]);

        $filiere->update($data);

        return back()->with('success', 'Filière mise à jour.');
    }

    // ── DESTROY ────────────────────────────────────────────
    public function destroy(Filiere $filiere)
    {
        $this->authorize('groupe-delete');

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