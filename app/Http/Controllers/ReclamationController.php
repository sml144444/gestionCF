<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReclamationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── INDEX ─────────────────────────────────────────────────
    // Stagiaire → ses propres réclamations
    // Admin / Gestionnaire → toutes les réclamations (avec filtres)
public function index(Request $request)
{
    $user = Auth::user();

    if ($user->can('reclamation-manage')) {
        // Admin / Gestionnaire : toutes les réclamations
        $query = Reclamation::with('stagiaire')
            ->orderByRaw("FIELD(status,'en_attente','traitee')")
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $reclamations = $query->paginate(15)->withQueryString();

        $stats = [
            'total'      => Reclamation::count(),
            'en_attente' => Reclamation::where('status', 'en_attente')->count(),
            'traitee'    => Reclamation::where('status', 'traitee')->count(),
        ];

        return view('reclamations.index', compact('reclamations', 'stats'));

    } elseif ($user->can('reclamation-list')) {
        // Stagiaire : uniquement les siennes
        $reclamations = Reclamation::where('id_user', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('reclamations.my', compact('reclamations'));
    }

    abort(403, 'Action non autorisée.');
}

    // ── CREATE ────────────────────────────────────────────────
    public function create()
    {
        $this->authorize('reclamation-create');
        return view('reclamations.create');
    }

    // ── STORE ─────────────────────────────────────────────────
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('reclamation-create');

        $validated = $request->validate([
            'type'        => 'required|in:note,absence,emploi,formateur,autre',
            'description' => 'required|string|min:10|max:1000',
        ]);

        Reclamation::create([
            'id_user'     => Auth::id(),
            'type'        => $validated['type'],
            'description' => $validated['description'],
            'status'      => 'en_attente',
        ]);

        return redirect()->route('reclamations.index')
            ->with('success', 'Votre réclamation a été soumise avec succès.');
    }

    // ── UPDATE STATUS (Admin / Gestionnaire) ──────────────────
    public function updateStatus(Request $request, Reclamation $reclamation): RedirectResponse
    {
        $this->authorize('reclamation-manage');

        $request->validate([
            'status' => 'required|in:en_attente,traitee',
        ]);

        $reclamation->update(['status' => $request->status]);

        return back()->with('success', 'Statut mis à jour.');
    }

    // ── DESTROY (Admin seulement) ─────────────────────────────
    public function destroy(Reclamation $reclamation): RedirectResponse
    {
        $this->authorize('reclamation-manage');

        $reclamation->delete();

        return back()->with('success', 'Réclamation supprimée.');
    }
}