<?php

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Filiere;
use App\Models\Groupe;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupeController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // LIST
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user        = Auth::user();
        $isFormateur = $user->role === 'formateur';

        $filiereFilter   = $request->get('filiere');
        $promoFilter     = $request->get('promo');

        $filieres        = Filiere::orderBy('name')->get();
        $selectedFiliere = $filiereFilter ? Filiere::find($filiereFilter) : null;

        // Build query
        $query = Groupe::with('filiere')
            ->withCount('stagiaires');

        // ── Formateur scope: only groups where they teach ────────────────────
        if ($isFormateur) {
            // Step 1: get module IDs assigned to this formateur (principal or remplaçant)
            $moduleIds = Module::where('id_user', $user->id)
                ->orWhere('id_user_remplacant', $user->id)
                ->pluck('id');

            // Step 2: get group IDs from emplois_du_temps linked to those modules
            $groupeIds = EmploiDuTemps::whereIn('id_module', $moduleIds)
                ->pluck('id_groupe')
                ->unique()
                ->values();

            $query->whereIn('id', $groupeIds);
        }
        // ─────────────────────────────────────────────────────────────────────

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
            'groupes', 'filieres', 'selectedFiliere', 'promos', 'isFormateur'
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