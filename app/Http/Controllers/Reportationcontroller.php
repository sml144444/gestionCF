<?php

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Reportation;
use App\Models\ReportationMessage;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportationController extends Controller
{
    // ── ADMIN/GESTIONNAIRE — see ALL requests ──────────────
    public function index(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        $status = trim($request->input('status', 'en_attente'));
        $search = trim($request->input('search', ''));

        $reportations = Reportation::with([
                'emploiDuTemps.groupe.filiere',
                'emploiDuTemps.module',
                'emploiDuTemps.salle',
                'formateur',
                'validePar',
                'messages',
                'messages.user',
            ])
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->when($search !== '', fn($q) => $q->whereHas('formateur', fn($u) =>
                $u->where('name', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'en_attente' => Reportation::where('status', 'en_attente')->count(),
            'valide'     => Reportation::where('status', 'valide')->count(),
            'refuse'     => Reportation::where('status', 'refuse')->count(),
        ];

        return view('reportations.index', compact('reportations', 'status', 'search', 'counts'));
    }

    // ── FORMATEUR — see ONLY their own requests ────────────
    public function myIndex(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('reportation-create')) {
            abort(403);
        }

        $status = trim($request->input('status', ''));

        $reportations = Reportation::with([
                'emploiDuTemps.groupe.filiere',
                'emploiDuTemps.module',
                'emploiDuTemps.salle',
                'validePar',
                'messages',
                'messages.user',
            ])
            ->where('id_user', auth()->id())
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'en_attente' => Reportation::where('id_user', auth()->id())->where('status', 'en_attente')->count(),
            'valide'     => Reportation::where('id_user', auth()->id())->where('status', 'valide')->count(),
            'refuse'     => Reportation::where('id_user', auth()->id())->where('status', 'refuse')->count(),
        ];

        return view('reportations.my', compact('reportations', 'status', 'counts'));
    }

    // ── GESTIONNAIRE — see ONLY assigned requests ──────────
    public function assignedIndex(Request $request)
    {
        if (! auth()->user()->hasPermissionTo('reportation-view-assigned')) {
            abort(403);
        }

        $status = trim($request->input('status', ''));
        $search = trim($request->input('search', ''));

        $reportations = Reportation::with([
                'emploiDuTemps.groupe.filiere',
                'emploiDuTemps.module',
                'emploiDuTemps.salle',
                'formateur',
                'validePar',
                'messages',
                'messages.user',
            ])
            ->where('assigned_to', auth()->id())
            ->when($status !== '', fn($q) => $q->where('status', $status))
            ->when($search !== '', fn($q) => $q->whereHas(
                'formateur', fn($u) => $u->where('name', 'like', "%{$search}%")
            ))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $counts = [
            'en_attente' => Reportation::where('assigned_to', auth()->id())->where('status', 'en_attente')->count(),
            'valide'     => Reportation::where('assigned_to', auth()->id())->where('status', 'valide')->count(),
            'refuse'     => Reportation::where('assigned_to', auth()->id())->where('status', 'refuse')->count(),
        ];

        return view('reportations.assigned', compact('reportations', 'status', 'search', 'counts'));
    }

    // ── FORMATEUR SUBMITS — reason only, NO date ───────────
    public function store(Request $request): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-create')) {
            abort(403);
        }

        $data = $request->validate([
            'id_emplois_du_temps' => 'required|exists:emplois_du_temps,id',
            'raison'              => 'required|string|min:10|max:1000',
        ]);

        $emploi = EmploiDuTemps::findOrFail($data['id_emplois_du_temps']);

        if ($emploi->id_user !== auth()->id()) {
            abort(403, 'Vous ne pouvez reporter que vos propres séances.');
        }

        if (Reportation::where('id_emplois_du_temps', $emploi->id)->where('status', 'en_attente')->exists()) {
            return back()->with('error', 'Une demande de report est déjà en attente pour cette séance.');
        }

        $reportation = Reportation::create([
            'id_emplois_du_temps' => $emploi->id,
            'id_user'             => auth()->id(),
            'raison'              => $data['raison'],
            'nouvelle_date_debut' => null,
            'nouvelle_date_fin'   => null,
            'status'              => 'en_attente',
        ]);

        if (class_exists(\App\Events\ReportationCreated::class)) {
            broadcast(new \App\Events\ReportationCreated($reportation));
        }

        $week = Carbon::parse($emploi->date_debut)->toDateString();
        $year = $emploi->groupe->annee ?? 1;

        return redirect()
            ->route('emplois.index', ['week' => $week, 'year' => $year])
            ->with('success', 'Demande de report envoyée. L\'administration choisira la nouvelle date.');
    }

    // ── ADMIN ACCEPTS — picks new date ─────────────────────
    public function accept(Request $request, Reportation $reportation): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        if ($reportation->status !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $data = $request->validate([
            'nouvelle_date_debut' => 'required|date|after:now',
            'nouvelle_date_fin'   => 'required|date|after:nouvelle_date_debut',
        ]);

        $emploi   = $reportation->emploiDuTemps;
        $newDebut = Carbon::parse($data['nouvelle_date_debut']);
        $newFin   = Carbon::parse($data['nouvelle_date_fin']);

        $overlap = EmploiDuTemps::whereIn('statut', ['actif', 'brouillon'])
            ->where('id', '!=', $emploi->id)
            ->where('date_debut', '<', $newFin)
            ->where('date_fin',   '>', $newDebut)
            ->where(fn($q) =>
                $q->where('id_groupe', $emploi->id_groupe)
                  ->orWhere('id_user', $emploi->id_user)
                  ->orWhere(fn($q2) =>
                      $q2->where('id_salle', $emploi->id_salle)
                         ->whereNotNull('id_salle')
                  )
            )
            ->exists();

        if ($overlap) {
            return back()->with('error', 'Conflit détecté sur ce créneau (groupe, formateur ou salle déjà occupé). Choisissez une autre date.');
        }

        $emploi->update([
            'date_debut' => $newDebut,
            'date_fin'   => $newFin,
            'jour'       => EmploiDuTempsController::DAYS[$newDebut->dayOfWeekIso] ?? null,
        ]);

        $reportation->update([
            'nouvelle_date_debut' => $newDebut,
            'nouvelle_date_fin'   => $newFin,
            'status'              => 'valide',
            'valide_by'           => auth()->id(),
        ]);

        return redirect()
            ->route('reportations.index', ['status' => 'valide'])
            ->with('success', 'Report accepté — séance déplacée au ' . $newDebut->translatedFormat('l d M Y') . ' à ' . $newDebut->format('H:i') . '.');
    }

    // ── REFUSE ─────────────────────────────────────────────
    public function refuse(Reportation $reportation): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        if ($reportation->status !== 'en_attente') {
            return back()->with('error', 'Cette demande a déjà été traitée.');
        }

        $reportation->update([
            'status'    => 'refuse',
            'valide_by' => auth()->id(),
        ]);

        return redirect()
            ->route('reportations.index', ['status' => 'refuse'])
            ->with('success', 'Demande refusée.');
    }

    // ── DELETE the original session ────────────────────────
    public function deleteSession(Reportation $reportation): RedirectResponse
    {
        if (! auth()->user()->hasPermissionTo('reportation-manage')) {
            abort(403);
        }

        $emploi = $reportation->emploiDuTemps;

        $reportation->update([
            'status'    => 'valide',
            'valide_by' => auth()->id(),
        ]);

        $emploi?->delete();

        return redirect()
            ->route('reportations.index')
            ->with('success', 'Séance supprimée suite à la demande de report.');
    }

    // ── ASSIGN gestionnaire ────────────────────────────────
    public function assign(Request $request, Reportation $reportation)
    {
        $request->validate(['assigned_to' => 'nullable|exists:users,id']);

        $reportation->update(['assigned_to' => $request->assigned_to]);

        if ($request->assigned_to) {
            event(new \App\Events\ReportationAssigned($reportation));
        }

        return back()->with('success', 'Reportation assignée avec succès.');
    }

    // ── SEND MESSAGE ───────────────────────────────────────
// ── SEND MESSAGE ───────────────────────────────────────────────
public function sendMessage(Request $request, Reportation $reportation): \Illuminate\Http\JsonResponse
{
    $user = auth()->user();

    $allowed = $reportation->id_user === $user->id
        || $reportation->assigned_to === $user->id
        || $user->hasPermissionTo('reportation-manage');

    if (! $allowed) abort(403);

    $data = $request->validate([
        'message'    => 'nullable|string|max:1000',
        'attachment' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,xls,xlsx,txt,zip',
    ]);

    if (empty($data['message']) && ! $request->hasFile('attachment')) {
        return response()->json(['error' => 'Message ou fichier requis.'], 422);
    }

    $attachmentPath = null;
    $attachmentName = null;
    $attachmentType = null;

    if ($request->hasFile('attachment')) {
        $file           = $request->file('attachment');
        $attachmentName = $file->getClientOriginalName();
        $attachmentType = str_starts_with($file->getMimeType(), 'image/') ? 'image' : 'file';
        $attachmentPath = $file->store('reportation-attachments', 'local');
    }

    $msg = ReportationMessage::create([
        'reportation_id'  => $reportation->id,
        'user_id'         => $user->id,
        'message'         => $data['message'] ?? null,
        'attachment_path' => $attachmentPath,
        'attachment_name' => $attachmentName,
        'attachment_type' => $attachmentType,
    ]);

    $msg->load('user');

    if (class_exists(\App\Events\ReportationMessageSent::class)) {
        broadcast(new \App\Events\ReportationMessageSent($msg))->toOthers();
    }

    // ══════════════════════════════════════════════════════════
    // NOTIFICATIONS
    // ══════════════════════════════════════════════════════════
    $senderName        = $user->name;
    $senderIsFormateur = $user->id === $reportation->id_user;
    $notifUrl          = route('reportations.my');

    $notifMessage = $attachmentPath && empty($data['message'])
        ? "📎 {$senderName} a joint un fichier dans la reportation #{$reportation->id}."
        : "💬 Nouveau message de {$senderName} dans la reportation #{$reportation->id}.";

    if ($senderIsFormateur) {
        // Formateur sent → notify assigned gestionnaire OR all managers
        $recipientIds = collect();

        if ($reportation->assigned_to) {
            $recipientIds->push($reportation->assigned_to);
        } else {
            $managerIds = \App\Models\User::permission('reportation-manage')
                ->where('id', '!=', $user->id)
                ->pluck('id');
            $recipientIds = $recipientIds->merge($managerIds);
        }

        foreach ($recipientIds->unique() as $recipientId) {
            $this->sendOrIncrementReportationNotif(
                (int) $recipientId,
                $reportation->id,
                $notifMessage,
                $notifUrl
            );
        }
    } else {
        // Admin / gestionnaire sent → notify the formateur
        $recipientId = $reportation->id_user;

        if ($recipientId && $recipientId !== $user->id) {
            $this->sendOrIncrementReportationNotif(
                (int) $recipientId,
                $reportation->id,
                $notifMessage,
                $notifUrl
            );
        }
    }
    // ══════════════════════════════════════════════════════════

    return response()->json($this->formatMessage($msg));
}

// ── PRIVATE HELPER ─────────────────────────────────────────────
private function sendOrIncrementReportationNotif(
    int    $recipientId,
    int    $reportationId,
    string $message,
    string $url
): void {
    $existing = \App\Models\UserNotification::where('user_id', $recipientId)
        ->where('type', 'reportation_reply')
        ->whereNull('read_at')
        ->whereJsonContains('data->reportation_id', $reportationId)
        ->latest()
        ->first();

    if ($existing) {
        $newCount = $existing->count + 1;
        $existing->update([
            'count'   => $newCount,
            'message' => "+{$newCount} nouveaux messages dans la reportation #{$reportationId}.",
        ]);
        broadcast(new \App\Events\NotificationUpdated($existing->fresh()))->toOthers();
    } else {
        $notif = \App\Models\UserNotification::create([
            'user_id' => $recipientId,
            'type'    => 'reportation_reply',
            'message' => $message,
            'url'     => $url,
            'count'   => 1,
            'data'    => [
                'reportation_id' => $reportationId,
                'sender_name'    => auth()->user()->name,
                'sender_role'    => auth()->user()->role,
            ],
        ]);
        broadcast(new \App\Events\NotificationCreated($notif->fresh()))->toOthers();
    }
}

    // ── GET MESSAGES ───────────────────────────────────────
    public function getMessages(Reportation $reportation): \Illuminate\Http\JsonResponse
    {
        $user = auth()->user();

        $allowed = $reportation->id_user === $user->id
            || $reportation->assigned_to === $user->id
            || $user->hasPermissionTo('reportation-manage');

        if (! $allowed) abort(403);

        $reportation->load('messages.user');

        return response()->json(
            $reportation->messages->map(fn($m) => $this->formatMessage($m))
        );
    }

    // ── SERVE ATTACHMENT (private storage) ─────────────────
    public function serveAttachment(ReportationMessage $message): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = auth()->user();
        $rp   = $message->reportation;

        $allowed = $rp->id_user === $user->id
            || $rp->assigned_to === $user->id
            || $user->hasPermissionTo('reportation-manage');

        if (! $allowed) abort(403);

        abort_unless(\Storage::disk('local')->exists($message->attachment_path), 404);

        return \Storage::disk('local')->download(
            $message->attachment_path,
            $message->attachment_name
        );
    }

    // ── HELPER ─────────────────────────────────────────────
private function formatMessage(ReportationMessage $m): array
{
    return [
        'id'              => $m->id,
        'message'         => $m->message,
        'user_id'         => $m->user_id,
        'user_name'       => $m->user->name,
        'created_at'      => $m->created_at->format('H:i'),
        'seen_at'         => $m->seen_at?->toISOString(), // ← nouveau
        'attachment_name' => $m->attachment_name,
        'attachment_type' => $m->attachment_type,
        'attachment_url'  => $m->attachment_path
            ? route('reportations.attachment', $m->id)
            : null,
    ];
}

    // ── MARK SEEN ──────────────────────────────────────────
public function markSeen(Reportation $reportation): \Illuminate\Http\JsonResponse
{
    $user = auth()->user();

    $allowed = $reportation->id_user === $user->id
        || $reportation->assigned_to === $user->id
        || $user->hasPermissionTo('reportation-manage');

    if (! $allowed) abort(403);

    // Récupère les IDs AVANT l'update
    $messageIds = $reportation->messages()
        ->where('user_id', '!=', $user->id)
        ->whereNull('seen_at')
        ->pluck('id')
        ->toArray();

    if (!empty($messageIds)) {
        $reportation->messages()
            ->whereIn('id', $messageIds)
            ->update(['seen_at' => now()]);

        // 🔥 Broadcaster l'event en temps réel
        if (class_exists(\App\Events\ReportationMessageSeen::class)) {
            broadcast(new \App\Events\ReportationMessageSeen(
                $reportation,
                $messageIds,
                $user->id
            ))->toOthers();
        }
    }

    return response()->json(['ok' => true, 'seen_ids' => $messageIds]);
}

// ── DELETE MESSAGE ─────────────────────────────────────
public function deleteMessage(ReportationMessage $message): \Illuminate\Http\JsonResponse
{
    $user = auth()->user();

    if ($message->user_id !== $user->id) abort(403);

    if ($message->seen_at !== null) {
        return response()->json(['error' => 'Message déjà vu, suppression impossible.'], 403);
    }

    // Delete attachment from storage if exists
    if ($message->attachment_path) {
        \Storage::disk('local')->delete($message->attachment_path);
    }

    $message->delete();

    return response()->json(['ok' => true]);
}

// ── EDIT MESSAGE (text only) ───────────────────────────
public function updateMessage(Request $request, ReportationMessage $message): \Illuminate\Http\JsonResponse
{
    $user = auth()->user();

    if ($message->user_id !== $user->id) abort(403);

    if ($message->seen_at !== null) {
        return response()->json(['error' => 'Message déjà vu, modification impossible.'], 403);
    }

    if ($message->attachment_path) {
        return response()->json(['error' => 'Impossible de modifier un message avec pièce jointe.'], 403);
    }

    $data = $request->validate(['message' => 'required|string|min:1|max:1000']);

    $message->update(['message' => $data['message']]);

    return response()->json(['ok' => true, 'message' => $data['message']]);
}
}