<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use Illuminate\Http\Request;

class FiliereController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:groupe-list');
    }

    public function index()
    {
        $filieres = Filiere::withCount(['groupes', 'modules', 'stagiaires'])
            ->with(['groupes' => fn($q) => $q->withCount('stagiaires')])
            ->orderBy('name')
            ->get();

        return view('filieres.index', compact('filieres'));
    }

    public function store(Request $request)
    {
        $this->authorize('groupe-create');

        $request->validate([
            'name'  => 'required|string|max:150|unique:filieres,name',
            'code'  => [
                'required', 'string', 'max:20',
                'unique:filieres,code',
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'duree' => 'required|integer|min:1|max:5',
        ], [
            'code.regex'  => 'Le code ne peut contenir que des lettres, chiffres, tirets et underscores.',
            'code.unique' => 'Ce code est déjà utilisé par une autre filière.',
        ]);

        Filiere::create([
            'name'  => $request->name,
            'code'  => strtoupper($request->code),
            'duree' => $request->duree,
        ]);

        return back()->with('success', 'Filière « ' . $request->name . ' » créée avec succès.');
    }

    public function update(Request $request, Filiere $filiere)
    {
        $this->authorize('groupe-edit');

        $request->validate([
            'name'  => 'required|string|max:150|unique:filieres,name,' . $filiere->id,
            'code'  => [
                'required', 'string', 'max:20',
                'unique:filieres,code,' . $filiere->id,
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'duree' => 'required|integer|min:1|max:5',
        ], [
            'code.regex'  => 'Le code ne peut contenir que des lettres, chiffres, tirets et underscores.',
            'code.unique' => 'Ce code est déjà utilisé par une autre filière.',
        ]);

        $filiere->update([
            'name'  => $request->name,
            'code'  => strtoupper($request->code),
            'duree' => $request->duree,
        ]);

        return back()->with('success', 'Filière mise à jour.');
    }

    public function destroy(Filiere $filiere)
    {
        $this->authorize('groupe-delete');

        $groupeCount = $filiere->groupes()->count();
        if ($groupeCount > 0) {
            return back()->with('error',
                'Impossible de supprimer : cette filière contient ' . $groupeCount . ' groupe(s).'
            );
        }

        $filiere->delete();
        return back()->with('success', 'Filière supprimée.');
    }
}