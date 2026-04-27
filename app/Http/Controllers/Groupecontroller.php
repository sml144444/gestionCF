<?php

namespace App\Http\Controllers;

use App\Models\Filiere;
use App\Models\Groupe;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class GroupeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:groupe-list');
    }

    // ── INDEX ─────────────────────────────────────────────
    public function index(Request $request): View
    {
        $user        = Auth::user();
        $isFormateur = $user->role === 'formateur';

        $filiereId = $request->get('filiere');
        $promo     = $request->get('promo');

        $filieres = Filiere::orderBy('name')->get();
        $promos   = Groupe::select('promo')
            ->distinct()
            ->orderByDesc('promo')
            ->pluck('promo');

        $groupes = Groupe::with(['filiere'])
            ->withCount('stagiaires')
            // ── Formateur: only groups that have at least one
            //    emploi_du_temps linked to one of their modules ──
            ->when($isFormateur, fn($q) => $q->whereHas(
                'emploisDuTemps', fn($eq) =>
                    $eq->whereHas('module', fn($mq) =>
                        $mq->where('id_user', $user->id)
                           ->orWhere('id_user_remplacant', $user->id)
                    )
            ))
            ->when($filiereId, fn($q) => $q->where('id_filiere', $filiereId))
            ->when($promo,     fn($q) => $q->where('promo', $promo))
            ->get()
            ->groupBy('id_filiere');

        $selectedFiliere = $filiereId ? Filiere::find($filiereId) : null;

        return view('groupes.index', compact(
            'groupes', 'filieres', 'promos',
            'selectedFiliere', 'isFormateur'
        ));
    }

    // ── STORE ─────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('groupe-create');

        $data = $request->validate([
            'id_filiere' => 'required|exists:filieres,id',
            'name'       => 'required|string|max:100',
            'code'       => 'required|string|max:30|unique:groupes,code',
            'annee'      => 'required|integer|in:1,2,3',
            'nbr_limit'  => 'required|integer|min:1|max:500',
            'promo'      => 'nullable|integer|min:2000|max:2099',
        ]);

        $exists = Groupe::where('id_filiere', $data['id_filiere'])
            ->where('name', $data['name'])
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Un groupe avec ce nom existe déjà dans cette filière.'])
                ->withInput();
        }

        Groupe::create($data);

        return back()->with('success', 'Groupe « ' . $data['name'] . ' » créé avec succès.');
    }

    // ── UPDATE ─────────────────────────────────────────────
    public function update(Request $request, Groupe $groupe): RedirectResponse
    {
        $this->authorize('groupe-edit');

        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'code'      => 'required|string|max:30|unique:groupes,code,' . $groupe->id,
            'annee'     => 'required|integer|in:1,2,3',
            'nbr_limit' => 'required|integer|min:1|max:500',
            'promo'     => 'nullable|integer|min:2000|max:2099',
        ]);

        $exists = Groupe::where('id_filiere', $groupe->id_filiere)
            ->where('name', $data['name'])
            ->where('id', '!=', $groupe->id)
            ->exists();

        if ($exists) {
            return back()
                ->withErrors(['name' => 'Un groupe avec ce nom existe déjà dans cette filière.'])
                ->withInput();
        }

        $groupe->update($data);

        return back()->with('success', 'Groupe « ' . $groupe->name . ' » mis à jour.');
    }

    // ── DESTROY ────────────────────────────────────────────
    public function destroy(Groupe $groupe): RedirectResponse
    {
        $this->authorize('groupe-delete');

        $stagiaireCount = $groupe->stagiaires()->count();
        if ($stagiaireCount > 0) {
            return back()->with('error',
                'Impossible de supprimer : ce groupe contient ' . $stagiaireCount . ' stagiaire(s). Désaffectez-les d\'abord.'
            );
        }

        $name = $groupe->name;
        $groupe->delete();

        return back()->with('success', 'Groupe « ' . $name . ' » supprimé.');
    }
}