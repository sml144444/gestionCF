<?php

namespace App\Http\Controllers;

use App\Models\Reclamation;
use App\Models\ReclamationMessage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use App\Events\ReclamationMessageSent;
use App\Events\ReclamationStatusUpdated;
use App\Events\ReclamationAssigned;      // ← ZID HADI
use App\Events\ReclamationCreated;       // ← W HADI
use App\Events\ReclamationDeleted;

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

    // 🔥 Broadcast nouvelle réclamation
    broadcast(new ReclamationCreated($reclamation->fresh()));

    return redirect()->route('reclamations.show', $reclamation)
        ->with('success', 'Réclamation soumise avec succès.');
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
// ── SEND MESSAGE ──────────────────────────────────────────
public function sendMessage(Request $request, Reclamation $reclamation)
{
    $user = Auth::user();

    if (! $reclamation->canReply($user)) {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Non autorisé.'], 403);
        }
        abort(403);
    }

    $request->validate([
        'message' => 'required|string|min:1|max:2000',
    ]);

    $msg = ReclamationMessage::create([
        'reclamation_id' => $reclamation->id,
        'sender_id'      => Auth::id(),
        'message'        => $request->message,
    ]);

    if ($reclamation->status === 'en_attente' && $user->id !== $reclamation->id_user) {
        $reclamation->update(['status' => 'en_cours']);
        // broadcast(new ReclamationStatusUpdated($reclamation->fresh()))->toOthers();
    }

    $reclamation->touch();
    broadcast(new ReclamationMessageSent($msg))->toOthers();

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => [
                'id'         => $msg->id,
                'message'    => $msg->message,
                'created_at' => $msg->created_at->format('H:i'),
                'sender'     => [
                    'id'   => $user->id,
                    'name' => $user->name,
                    'role' => $user->role,
                ],
            ],
        ]);
    }

    return redirect()
        ->route('reclamations.show', $reclamation)
        ->with('success', 'Message envoyé.');
}
    // ── ASSIGN (admin only) ───────────────────────────────────
public function assign(Request $request, Reclamation $reclamation): RedirectResponse
{
    $this->authorize('reclamation-manage');

    $request->validate([
        'assigned_to' => 'nullable|exists:users,id',
    ]);

    $reclamation->update(['assigned_to' => $request->assigned_to ?: null]);

    // 🔥 Broadcast l-assigné
    if ($request->assigned_to) {
        broadcast(new ReclamationAssigned($reclamation->fresh()));
    }

    $assignee = $request->assigned_to
        ? User::find($request->assigned_to)?->name
        : null;

    return back()->with('success', $assignee
        ? "Réclamation assignée à {$assignee}."
        : 'Assignation retirée.');
}

    // ── UPDATE STATUS (admin / gestionnaire) ──────────────────
 // ── UPDATE STATUS ──────────────────────────────────────────
public function updateStatus(Request $request, Reclamation $reclamation): RedirectResponse
{
    $this->authorize('reclamation-manage');

    $request->validate([
        'status' => 'required|in:en_attente,en_cours,traite',
    ]);

    $reclamation->update(['status' => $request->status]);

    // 🔥 Broadcast le changement de statut
    broadcast(new ReclamationStatusUpdated($reclamation->fresh()));

    $label = Reclamation::STATUSES[$request->status]['label'] ?? $request->status;

    return back()->with('success', "Statut mis à jour : {$label}.");
}

    // ── DESTROY (admin / gestionnaire only) ───────────────────
// ── DESTROY ───────────────────────────────────────────────
public function destroy(Reclamation $reclamation): RedirectResponse
{
    $this->authorize('reclamation-manage');

    $stagiaireId   = $reclamation->id_user;
    $reclamationId = $reclamation->id;

    $reclamation->messages()->delete();
    $reclamation->delete();

    // 🔥 Broadcast suppression
    broadcast(new \App\Events\ReclamationDeleted($reclamationId, $stagiaireId));

    return redirect()->route('reclamations.index')
        ->with('success', 'Réclamation #' . $reclamationId . ' supprimée avec succès.');
}

// ── STORE ─────────────────────────────────────────────────

}