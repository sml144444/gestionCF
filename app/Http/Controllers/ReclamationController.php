<?php
// app/Http/Controllers/ReclamationController.php

namespace App\Http\Controllers;

use App\Events\ReclamationAssigned;
use App\Events\ReclamationCreated;
use App\Events\ReclamationDeleted;
use App\Events\ReclamationMessageDeleted;
use App\Events\ReclamationMessageSent;
use App\Events\ReclamationMessageUpdated;
use App\Events\ReclamationStatusUpdated;
use App\Models\Reclamation;
use App\Models\ReclamationMessage;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\UserNotification;
class ReclamationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    // ── INDEX ─────────────────────────────────────────────────
    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->can('reclamation-manage')) {
            $query = Reclamation::with(['stagiaire', 'assignee'])
                ->withCount('messages')
                ->orderByRaw("FIELD(status,'en_attente','en_cours','traite')")
                ->orderBy('updated_at', 'desc');

            if ($request->filled('status')) $query->where('status', $request->status);
            if ($request->filled('type'))   $query->where('type',   $request->type);

            $reclamations    = $query->paginate(15)->withQueryString();
            $stats           = [
                'total'      => Reclamation::count(),
                'en_attente' => Reclamation::where('status', 'en_attente')->count(),
                'en_cours'   => Reclamation::where('status', 'en_cours')->count(),
                'traite'     => Reclamation::where('status', 'traite')->count(),
            ];
            $assignableUsers = User::whereIn('role', ['formateur', 'gestionnaire'])
                ->orderBy('name')->get();

            return view('reclamations.index', compact('reclamations', 'stats', 'assignableUsers'));
        }

        if (in_array($user->role, ['formateur', 'gestionnaire'])) {
            $query = Reclamation::with(['stagiaire'])
                ->withCount('messages')
                ->where('assigned_to', $user->id)
                ->orderByRaw("FIELD(status,'en_attente','en_cours','traite')")
                ->orderBy('updated_at', 'desc');

            if ($request->filled('status')) $query->where('status', $request->status);

            $reclamations = $query->paginate(15)->withQueryString();
            $stats        = [
                'total'      => Reclamation::where('assigned_to', $user->id)->count(),
                'en_attente' => Reclamation::where('assigned_to', $user->id)->where('status', 'en_attente')->count(),
                'en_cours'   => Reclamation::where('assigned_to', $user->id)->where('status', 'en_cours')->count(),
                'traite'     => Reclamation::where('assigned_to', $user->id)->where('status', 'traite')->count(),
            ];

            return view('reclamations.assigned', compact('reclamations', 'stats'));
        }

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

// ── STORE ─────────────────────────────────────────────────────────
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
 
    $reclamation->load('stagiaire');
    $url       = route('reclamations.show', $reclamation);
    $typeLabel = Reclamation::TYPES[$reclamation->type]['label'] ?? $reclamation->type;
    $typeIcon  = Reclamation::TYPES[$reclamation->type]['icon']  ?? '📌';
 
    // ── Broadcast WebSocket event (existing) ──────────────────────
    broadcast(new ReclamationCreated($reclamation->fresh()));
 
    // ── 1. STAGIAIRE — confirmation of submission ─────────────────
    NotificationService::send(
        Auth::user(),
        'reclamation_reply',
        'Votre réclamation a été envoyée avec succès.',
        $url,
        ['reclamation_id' => $reclamation->id]
    );
 
    // ── 2. Notify only users who CAN manage reclamations ──────────
    //    Uses Spatie's scope → no more guessing by role name.
    //    If gestionnaire doesn't have reclamation-manage → not notified.
    $managers = User::permission('reclamation-manage')
        ->where('id', '!=', Auth::id())   // don't notify yourself
        ->get();
 
    foreach ($managers as $manager) {
        NotificationService::send(
            $manager,
            'reclamation_reply',
            "Nouvelle réclamation reçue de {$reclamation->stagiaire->name} ({$typeIcon} {$typeLabel}).",
            $url,
            ['reclamation_id' => $reclamation->id]
        );
    }
 
    return redirect()->route('reclamations.show', $reclamation)
        ->with('success', 'Réclamation soumise avec succès.');
}
 
    // ── SHOW ──────────────────────────────────────────────────
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
// ── SEND MESSAGE ──────────────────────────────────────────────────

public function sendMessage(Request $request, Reclamation $reclamation): \Illuminate\Http\JsonResponse
{
    // ── Authorization ────────────────────────────────────────
    if (! $reclamation->isAccessibleBy(Auth::user())) {
        abort(403);
    }

    if (! $reclamation->canReply(Auth::user())) {
        return response()->json(['error' => 'Cette réclamation est clôturée.'], 403);
    }

    // ── Validation ───────────────────────────────────────────
    $request->validate([
        'message'    => 'nullable|string|max:2000',
        'attachment' => 'nullable|file|max:10240|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip',
    ]);

    if (! $request->filled('message') && ! $request->hasFile('attachment')) {
        return response()->json(['error' => 'Veuillez écrire un message ou joindre un fichier.'], 422);
    }

    // ── Upload ───────────────────────────────────────────────
    $attachmentPath = null;
    $attachmentName = null;
    $attachmentMime = null;

    if ($request->hasFile('attachment')) {
        $file           = $request->file('attachment');
        $attachmentName = $file->getClientOriginalName();
        $attachmentMime = $file->getMimeType();
        $attachmentPath = $file->store('reclamation-attachments', 'public');
    }

    // ── Create message ───────────────────────────────────────
    $message = ReclamationMessage::create([
        'reclamation_id'  => $reclamation->id,
        'sender_id'       => Auth::id(),
        'message'         => $request->filled('message') ? $request->input('message') : null,
        'attachment_path' => $attachmentPath,
        'attachment_name' => $attachmentName,
        'attachment_mime' => $attachmentMime,
    ]);

    // ── Broadcast ────────────────────────────────────────────
    broadcast(new ReclamationMessageSent($message))->toOthers();

    // ── Auto status: en_attente → en_cours ───────────────────
    if ($reclamation->status === 'en_attente' && Auth::user()->can('reclamation-manage')) {
        $reclamation->update(['status' => 'en_cours']);
    }

    // ── Notification ─────────────────────────────────────────
    $notifMessage = $attachmentPath && ! $request->filled('message')
        ? '📎 Vous avez reçu un fichier joint.'
        : '💬 Nouveau message de ' . Auth::user()->name . '.';

    $senderIsStagiaire = Auth::id() === $reclamation->id_user;

    if ($senderIsStagiaire) {
        // ── Stagiaire sent ────────────────────────────────────
        $recipients = collect();

        if ($reclamation->assigned_to) {
            // Assigned → notify the assignee only
            $recipients->push($reclamation->assigned_to);
        } else {
            // Not assigned → notify admins only (not gestionnaire/formateur)
            $adminIds = User::where('role', 'admin')
                ->where('id', '!=', Auth::id())
                ->pluck('id');
            $recipients = $recipients->merge($adminIds);
        }

        foreach ($recipients->unique() as $recipientId) {
            $existing = UserNotification::where('user_id', $recipientId)
                ->where('type', 'reclamation_reply')
                ->whereNull('read_at')
                ->whereJsonContains('data->reclamation_id', $reclamation->id)
                ->latest()
                ->first();

            if ($existing) {
                $newCount = $existing->count + 1;
                $existing->update([
                    'count'   => $newCount,
                    'message' => "+{$newCount} nouveaux messages dans la réclamation #{$reclamation->id}.",
                ]);
                broadcast(new \App\Events\NotificationUpdated($existing))->toOthers();
            } else {
                $notif = UserNotification::create([
                    'user_id' => $recipientId,
                    'type'    => 'reclamation_reply',
                    'message' => $notifMessage,
                    'url'     => route('reclamations.show', $reclamation),
                    'count'   => 1,
                    'data'    => [
                        'reclamation_id' => $reclamation->id,
                        'sender_name'    => Auth::user()->name,
                        'sender_role'    => Auth::user()->role,
                    ],
                ]);
                broadcast(new \App\Events\NotificationCreated($notif))->toOthers();
            }
        }

    } else {
        // ── Staff / admin sent → notify the stagiaire ────────
        $recipientId = $reclamation->id_user;

        if ($recipientId) {
            $existing = UserNotification::where('user_id', $recipientId)
                ->where('type', 'reclamation_reply')
                ->whereNull('read_at')
                ->whereJsonContains('data->reclamation_id', $reclamation->id)
                ->latest()
                ->first();

            if ($existing) {
                $newCount = $existing->count + 1;
                $existing->update([
                    'count'   => $newCount,
                    'message' => "+{$newCount} nouveaux messages dans la réclamation #{$reclamation->id}.",
                ]);
                broadcast(new \App\Events\NotificationUpdated($existing))->toOthers();
            } else {
                $notif = UserNotification::create([
                    'user_id' => $recipientId,
                    'type'    => 'reclamation_reply',
                    'message' => $notifMessage,
                    'url'     => route('reclamations.show', $reclamation),
                    'count'   => 1,
                    'data'    => [
                        'reclamation_id' => $reclamation->id,
                        'sender_name'    => Auth::user()->name,
                        'sender_role'    => Auth::user()->role,
                    ],
                ]);
                broadcast(new \App\Events\NotificationCreated($notif))->toOthers();
            }
        }
    }

    // ── Return JSON for AJAX ─────────────────────────────────
    return response()->json([
        'ok'              => true,
        'id'              => $message->id,
        'message'         => $message->message,
        'created_at'      => $message->created_at->format('H:i'),
        'attachment_path' => $message->attachment_path
                                ? asset('storage/' . $message->attachment_path)
                                : null,
        'attachment_name' => $message->attachment_name,
        'attachment_mime' => $message->attachment_mime,
        'sender'          => [
            'id'   => Auth::id(),
            'name' => Auth::user()->name,
            'role' => Auth::user()->role,
        ],
    ]);
}
    // ── ASSIGN ────────────────────────────────────────────────
    public function assign(Request $request, Reclamation $reclamation): RedirectResponse
    {
        $this->authorize('reclamation-manage');

        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $reclamation->update(['assigned_to' => $request->assigned_to ?: null]);
        $reclamation->load('stagiaire', 'assignee');

        $url = route('reclamations.show', $reclamation);

        // ── Broadcast WebSocket event (existing) ──────────────
        if ($request->assigned_to) {
            broadcast(new ReclamationAssigned($reclamation->fresh()));
        }

        if ($request->assigned_to && $reclamation->assignee) {

            // ── 1. GESTIONNAIRE / FORMATEUR — assigned to them ─
            NotificationService::send(
                $reclamation->assignee,
                'reclamation_assigned',
                "Une réclamation vous a été assignée (#" . $reclamation->id . ").",
                $url,
                ['reclamation_id' => $reclamation->id]
            );

            // ── 2. STAGIAIRE — their reclamation has been assigned
            if ($reclamation->stagiaire) {
                NotificationService::send(
                    $reclamation->stagiaire,
                    'reclamation_assigned',
                    "Votre réclamation a été assignée à un responsable.",
                    $url,
                    ['reclamation_id' => $reclamation->id]
                );
            }

            // ── 3. ADMIN who performed the action — confirmation ─
            NotificationService::send(
                Auth::user(),
                'reclamation_assigned',
                "Réclamation assignée avec succès à {$reclamation->assignee->name}.",
                $url,
                ['reclamation_id' => $reclamation->id]
            );
        }

        $assigneeName = $request->assigned_to
            ? User::find($request->assigned_to)?->name
            : null;

        return back()->with('success', $assigneeName
            ? "Réclamation assignée à {$assigneeName}."
            : 'Assignation retirée.');
    }

    // ── UPDATE STATUS ─────────────────────────────────────────
 // ── UPDATE STATUS ─────────────────────────────────────────
public function updateStatus(Request $request, Reclamation $reclamation): RedirectResponse|\Illuminate\Http\JsonResponse
{
    $user       = Auth::user();
    $isStaff    = $user->can('reclamation-manage');
    $isAssigned = $reclamation->assigned_to === $user->id;

    if (! $isStaff && ! $isAssigned) {
        if ($request->expectsJson()) {
            return response()->json(['ok' => false, 'message' => 'Non autorisé.'], 403);
        }
        abort(403);
    }

    // Assigned users (non-admin) may only set en_cours or traite
    $allowed = $isStaff
        ? ['en_attente', 'en_cours', 'traite', 'refuse']
        : ['en_cours', 'traite'];

    $request->validate([
        'status' => 'required|in:' . implode(',', $allowed),
    ]);

    $reclamation->update(['status' => $request->status]);
    $reclamation->load('stagiaire');

    // ── Broadcast WebSocket event ──────────────────────────
    broadcast(new ReclamationStatusUpdated($reclamation->fresh()));

    $url = route('reclamations.show', $reclamation);

    // ── Notify ONLY when status = traite ───────────────────
    if ($request->status === 'traite') {

        // 1. Notify stagiaire ──────────────────────────────
        if ($reclamation->stagiaire) {
            NotificationService::send(
                $reclamation->stagiaire,
                'reclamation_status',
                "✅ Votre réclamation #{$reclamation->id} a été traitée.",
                $url,
                ['reclamation_id' => $reclamation->id]
            );
        }

        // 2. Notify admins (only if action done by gestionnaire/formateur)
        if ($user->role !== 'admin') {
            $admins = User::where('role', 'admin')->get();
            foreach ($admins as $admin) {
                NotificationService::send(
                    $admin,
                    'reclamation_status',
                    "✅ La réclamation #{$reclamation->id} a été traitée par {$user->name}.",
                    $url,
                    ['reclamation_id' => $reclamation->id]
                );
            }
        }
    }

    $cfg   = \App\Models\Reclamation::STATUSES[$request->status] ?? [];
    $label = $cfg['label'] ?? $request->status;

    // ── JSON response for AJAX ─────────────────────────────
    if ($request->expectsJson()) {
        return response()->json([
            'ok'    => true,
            'badge' => $cfg,
        ]);
    }

    return back()->with('success', "Statut mis à jour : {$label}.");
}

    // ── DESTROY ───────────────────────────────────────────────
    public function destroy(Reclamation $reclamation): RedirectResponse
    {
        $this->authorize('reclamation-manage');

        $stagiaireId   = $reclamation->id_user;
        $reclamationId = $reclamation->id;
        $stagiaire     = $reclamation->stagiaire; // load before delete

        $reclamation->messages()->delete();
        $reclamation->delete();

        // ── Broadcast WebSocket event (existing) ──────────────
        broadcast(new ReclamationDeleted($reclamationId, $stagiaireId));

        // ── Notify stagiaire their reclamation was deleted ─────
        if ($stagiaire) {
            NotificationService::send(
                $stagiaire,
                'reclamation_deleted',
                "Votre réclamation #{$reclamationId} a été supprimée par l'administration.",
                null,
                ['reclamation_id' => $reclamationId]
            );
        }

        return redirect()->route('reclamations.index')
            ->with('success', 'Réclamation #' . $reclamationId . ' supprimée avec succès.');
    }

    // ── MARK MESSAGES AS SEEN ─────────────────────────────────
 public function markSeen(Reclamation $reclamation)
{
    $user = Auth::user();

    // Get IDs of messages that were unseen before we update them
    $unseenIds = $reclamation->messages()
        ->where('sender_id', '!=', $user->id)
        ->whereNull('seen_at')
        ->pluck('id')
        ->toArray();

    if (empty($unseenIds)) {
        return response()->json(['ok' => true]);
    }

    // Mark them as seen
    $reclamation->messages()
        ->whereIn('id', $unseenIds)
        ->update(['seen_at' => now()]);

    // Broadcast to the other user so their ✓✓ updates
    broadcast(new \App\Events\ReclamationMessageSeen(
        $reclamation->id,
        $unseenIds,
        $user->name,
        $user->role
    ))->toOthers();

    return response()->json(['ok' => true]);
}

    // ── DELETE MESSAGE ────────────────────────────────────────
public function deleteMessage(Request $request, Reclamation $reclamation, ReclamationMessage $message)
{
    $user = Auth::user();

    if (! $message->canEditOrDelete($user)) {
        return response()->json([
            'error' => 'Ce message a déjà été vu et ne peut plus être supprimé.'
        ], 403);
    }

    $messageId = $message->id;

    // ── Delete the physical file from storage if exists ──────
    if ($message->attachment_path) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($message->attachment_path);
    }

    $message->delete();

    broadcast(new ReclamationMessageDeleted($reclamation->id, $messageId))->toOthers();

    return response()->json(['ok' => true]);
}

    // ── EDIT MESSAGE ──────────────────────────────────────────
   public function editMessage(Request $request, Reclamation $reclamation, ReclamationMessage $message)
{
    $user = Auth::user();

    if (! $message->canEdit($user)) {  // ← changed from canEditOrDelete
        return response()->json(['error' => 'Ce message ne peut pas être modifié.'], 403);
    }

    $request->validate(['message' => 'required|string|min:1|max:2000']);

    $message->update([
        'message'   => $request->message,
        'edited_at' => now(),
    ]);

    broadcast(new ReclamationMessageUpdated($message->fresh()))->toOthers();

    return response()->json([
        'ok'        => true,
        'message'   => $message->message,
        'edited_at' => $message->edited_at->format('H:i'),
    ]);
}
}