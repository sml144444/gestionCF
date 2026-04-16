<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Http\Request;

class GroupeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:groupe-list');
    }

    public function index(Request $request)
    {
        $filiereId = $request->get('filiere');

        $filieres = Filiere::orderBy('name')->get();

        $groupes = Groupe::with(['filiere', 'stagiaires'])
            ->withCount('stagiaires')
            ->when($filiereId, fn($q) => $q->where('id_filiere', $filiereId))
            ->orderBy('id_filiere')
            ->orderBy('annee')
            ->orderBy('name')
            ->get()
            ->groupBy('id_filiere');

        $selectedFiliere = $filiereId ? Filiere::find($filiereId) : null;

        return view('groupes.index', compact('groupes', 'filieres', 'selectedFiliere'));
    }

    public function store(Request $request)
    {
        $this->authorize('groupe-create');

        $request->validate([
            'id_filiere' => 'required|exists:filieres,id',
            'name'       => 'required|string|max:50',
            'code'       => [
                'required', 'string', 'max:30',
                'unique:groupes,code',
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'annee'      => 'required|integer|in:1,2,3',
            'nbr_limit'  => 'required|integer|min:1|max:100',
        ], [
            'code.regex'  => 'Le code ne peut contenir que des lettres, chiffres, tirets et underscores.',
            'code.unique' => 'Ce code est déjà utilisé par un autre groupe.',
        ]);

        // Check unique name within filière
        $exists = Groupe::where('id_filiere', $request->id_filiere)
            ->where('name', $request->name)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Un groupe avec ce nom existe déjà dans cette filière.'])
                ->withInput();
        }

        Groupe::create([
            'id_filiere' => $request->id_filiere,
            'id_option'  => null,
            'name'       => $request->name,
            'code'       => strtoupper($request->code),
            'annee'      => $request->annee,
            'nbr_limit'  => $request->nbr_limit,
        ]);

        return back()->with('success', 'Groupe « ' . $request->name . ' » créé.');
    }

    public function update(Request $request, Groupe $groupe)
    {
        $this->authorize('groupe-edit');

        $request->validate([
            'name'      => 'required|string|max:50',
            'code'      => [
                'required', 'string', 'max:30',
                'unique:groupes,code,' . $groupe->id,
                'regex:/^[A-Za-z0-9\-_]+$/',
            ],
            'annee'     => 'required|integer|in:1,2',
            'nbr_limit' => 'required|integer|min:1|max:100',
        ], [
            'code.regex'  => 'Le code ne peut contenir que des lettres, chiffres, tirets et underscores.',
            'code.unique' => 'Ce code est déjà utilisé par un autre groupe.',
        ]);

        // Check unique name within filière (excluding self)
        $exists = Groupe::where('id_filiere', $groupe->id_filiere)
            ->where('name', $request->name)
            ->where('id', '!=', $groupe->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name_' . $groupe->id => 'Un groupe avec ce nom existe déjà dans cette filière.'])
                ->withInput();
        }

        $groupe->update([
            'name'      => $request->name,
            'code'      => strtoupper($request->code),
            'annee'     => $request->annee,
            'nbr_limit' => $request->nbr_limit,
        ]);

        return back()->with('success', 'Groupe « ' . $groupe->name . ' » mis à jour.');
    }

    public function destroy(Groupe $groupe)
    {
        $this->authorize('groupe-delete');

        $stagiaireCount = $groupe->stagiaires()->count();
        if ($stagiaireCount > 0) {
            return back()->with('error',
                'Impossible de supprimer : ce groupe contient ' . $stagiaireCount . ' stagiaire(s).'
            );
        }

        $name = $groupe->name;
        $groupe->delete();
        return back()->with('success', 'Groupe « ' . $name . ' » supprimé.');
    }
}