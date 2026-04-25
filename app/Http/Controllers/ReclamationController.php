<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use App\Models\ReclamationMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReclamationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── INDEX — role-based list ───────────────────────────────
    public function index(Request $request)
    {
        $user = Auth::user();

        // ── Admin / Gestionnaire : all reclamations ──────────
        if ($user->can('reclamation-manage')) {
            $query = Reclamation::with(['stagiaire', 'assignee'])
                ->withCount('messages')
                ->orderByRaw("FIELD(status,'en_attente','en_cours','traite')")
                ->orderBy('updated_at', 'desc');

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
                'en_cours'   => Reclamation::where('status', 'en_cours')->count(),
                'traite'     => Reclamation::where('status', 'traite')->count(),
            ];

            $assignableUsers = User::whereIn('role', ['formateur', 'gestionnaire'])
                ->orderBy('name')
                ->get();

            return view('reclamations.index', compact(
                'reclamations', 'stats', 'assignableUsers'
            ));
        }

        // ── Formateur / Gestionnaire (without manage) : assigned only ──
        if (in_array($user->role, ['formateur', 'gestionnaire'])) {
            $query = Reclamation::with(['stagiaire'])
                ->withCount('messages')
                ->where('assigned_to', $user->id)
                ->orderByRaw("FIELD(status,'en_attente','en_cours','traite')")
                ->orderBy('updated_at', 'desc');

            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            $reclamations = $query->paginate(15)->withQueryString();

            $stats = [
                'total'      => Reclamation::where('assigned_to', $user->id)->count(),
                'en_attente' => Reclamation::where('assigned_to', $user->id)->where('status', 'en_attente')->count(),
                'en_cours'   => Reclamation::where('assigned_to', $user->id)->where('status', 'en_cours')->count(),
                'traite'     => Reclamation::where('assigned_to', $user->id)->where('status', 'traite')->count(),
            ];

            return view('reclamations.assigned', compact('reclamations', 'stats'));
        }

        // ── Stagiaire : own reclamations ─────────────────────
        if ($user->can('reclamation-list')) {
            $reclamations = Reclamation::where('id_user', $user->id)
                ->withCount('messages')
                ->orderBy('updated_at', 'desc')
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
            'description' => ['required', 'string', 'min:10', 'max:1000', 'regex:/^\S{1,50}(\s\S{1,50})*$/u'],
        ]);

        $reclamation = Reclamation::create([
            'id_user'     => Auth::id(),
            'type'        => $validated['type'],
            'description' => $validated['description'],
            'status'      => 'en_attente',
        ]);

        return redirect()->route('reclamations.show', $reclamation)
            ->with('success', 'Réclamation soumise avec succès. L\'équipe vous répondra prochainement.');
    }

    // ── SHOW (conversation thread) ────────────────────────────
    public function show(Reclamation $reclamation)
    {
        $user = Auth::user();

        if (! $reclamation->isAccessibleBy($user)) {
            abort(403, 'Vous n\'avez pas accès à cette réclamation.');
        }

        $reclamation->load(['stagiaire', 'assignee', 'messages.sender']);

        $assignableUsers = $user->can('reclamation-manage')
            ? User::whereIn('role', ['formateur', 'gestionnaire'])->orderBy('name')->get()
            : collect();

        return view('reclamations.show', compact('reclamation', 'assignableUsers'));
    }

    // ── SEND MESSAGE ──────────────────────────────────────────
    public function sendMessage(Request $request, Reclamation $reclamation): RedirectResponse
    {
        $user = Auth::user();

        if (! $reclamation->canReply($user)) {
            abort(403, 'Vous ne pouvez pas répondre à cette réclamation.');
        }

        $request->validate([
            'message' => 'required|string|min:1|max:2000',
        ]);

        ReclamationMessage::create([
            'reclamation_id' => $reclamation->id,
            'sender_id'      => Auth::id(),
            'message'        => $request->message,
        ]);

        // Auto-move to en_cours if still en_attente and a staff member replies
        if ($reclamation->status === 'en_attente'
            && $user->id !== $reclamation->id_user) {
            $reclamation->update(['status' => 'en_cours']);
        }

        $reclamation->touch(); // update updated_at so it bubbles up in list

        return redirect()
            ->route('reclamations.show', $reclamation)
            ->with('success', 'Message envoyé.')
            ->withFragment('messages-end');
    }

    // ── ASSIGN (admin only) ───────────────────────────────────
    public function assign(Request $request, Reclamation $reclamation): RedirectResponse
    {
        $this->authorize('reclamation-manage');

        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $reclamation->update(['assigned_to' => $request->assigned_to ?: null]);

        $assignee = $request->assigned_to
            ? User::find($request->assigned_to)?->name
            : null;

        $msg = $assignee
            ? "Réclamation assignée à {$assignee}."
            : 'Assignation retirée.';

        return back()->with('success', $msg);
    }

    // ── UPDATE STATUS (admin / gestionnaire) ──────────────────
    public function updateStatus(Request $request, Reclamation $reclamation): RedirectResponse
    {
        $this->authorize('reclamation-manage');

        $request->validate([
            'status' => 'required|in:en_attente,en_cours,traite',
        ]);

        $reclamation->update(['status' => $request->status]);

        $label = Reclamation::STATUSES[$request->status]['label'] ?? $request->status;

        return back()->with('success', "Statut mis à jour : {$label}.");
    }

    // ── DESTROY (admin / gestionnaire only) ───────────────────
    public function destroy(Reclamation $reclamation): RedirectResponse
    {
        $this->authorize('reclamation-manage');
        
        // Supprimer d'abord tous les messages associés
        $reclamation->messages()->delete();
        
        // Puis supprimer la réclamation
        $reclamation->delete();

        return redirect()->route('reclamations.index')
            ->with('success', 'Réclamation #' . $reclamation->id . ' supprimée avec succès.');
    }
}