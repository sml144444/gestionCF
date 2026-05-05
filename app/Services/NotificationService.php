<?php
// app/Services/NotificationService.php

namespace App\Services;

use App\Events\NotificationCreated;
use App\Events\NotificationUpdated;
use App\Models\User;
use App\Models\UserNotification;

class NotificationService
{
    /**
     * Create a new notification or increment an existing unread one
     * of the same type + reclamation_id for the same user.
     *
     * This prevents stacking: instead of 5 bell items for 5 messages,
     * the user sees one item: "3 nouveaux messages dans la réclamation #X"
     */
    public static function send(
        User    $recipient,
        string  $type,
        string  $message,
        ?string $url  = null,
        array   $data = []
    ): UserNotification {

        // ── Try to find an existing unread notification to group into ──
        $existing = self::findGroupable($recipient->id, $type, $data);

        if ($existing) {
            return self::increment($existing, $data);
        }

        // ── No existing one → create fresh ────────────────────────────
        $notification = UserNotification::create([
            'user_id' => $recipient->id,
            'type'    => $type,
            'message' => $message,
            'url'     => $url,
            'data'    => $data ?: null,
            'count'   => 1,
        ]);

        broadcast(new NotificationCreated($notification));

        return $notification;
    }

    // ── Private: find a groupable unread notification ──────────────────
    private static function findGroupable(int $userId, string $type, array $data): ?UserNotification
    {
        // Only group message notifications (not assignment, status change, etc.)
        $groupableTypes = ['reclamation_reply'];

        if (! in_array($type, $groupableTypes)) {
            return null;
        }

        // Must have a reclamation_id to group on
        $reclamationId = $data['reclamation_id'] ?? null;
        if (! $reclamationId) {
            return null;
        }

        // Find unread, same type, same reclamation
        return UserNotification::forUser($userId)
            ->unread()
            ->where('type', $type)
            ->whereJsonContains('data->reclamation_id', $reclamationId)
            ->latest()
            ->first();
    }

    // ── Private: increment existing notification ───────────────────────
    private static function increment(UserNotification $notification, array $data): UserNotification
    {
        $newCount = $notification->count + 1;

        // Build a new message that reflects the count
        $reclamationId = $data['reclamation_id'] ?? ($notification->data['reclamation_id'] ?? null);

        $message = $reclamationId
            ? "{$newCount} nouveaux messages dans la réclamation #{$reclamationId}."
            : "{$newCount} nouveaux messages.";

        $notification->update([
            'count'   => $newCount,
            'message' => $message,
        ]);

        // Broadcast an update (not a create) so frontend patches the existing item
        broadcast(new NotificationUpdated($notification->fresh()));

        return $notification;
    }

    // ── Convenience wrappers (unchanged API) ───────────────────────────

    public static function reclamationReply(User $recipient, int $reclamationId, string $senderName, string $url): UserNotification
    {
        // First message uses the sender's name; subsequent increments use the count message.
        // We check here so the first notification has a personal message.
        $existing = self::findGroupable($recipient->id, 'reclamation_reply', ['reclamation_id' => $reclamationId]);

        $message = $existing
            ? ($existing->count + 1) . " nouveaux messages dans la réclamation #{$reclamationId}."
            : "{$senderName} a répondu à la réclamation #{$reclamationId}.";

        return self::send(
            $recipient,
            'reclamation_reply',
            $message,
            $url,
            ['reclamation_id' => $reclamationId]
        );
    }

    public static function reclamationAssigned(User $assignee, int $reclamationId, string $stagiaireName, string $url): UserNotification
    {
        return self::send(
            $assignee,
            'reclamation_assigned',
            "Réclamation #{$reclamationId} de {$stagiaireName} vous a été assignée.",
            $url,
            ['reclamation_id' => $reclamationId]
        );
    }

    public static function reclamationStatusChanged(User $recipient, int $reclamationId, string $statusLabel, string $url): UserNotification
    {
        return self::send(
            $recipient,
            'reclamation_status',
            "Votre réclamation #{$reclamationId} est maintenant : {$statusLabel}.",
            $url,
            ['reclamation_id' => $reclamationId]
        );
    }

    public static function reclamationDeleted(User $recipient, int $reclamationId): UserNotification
    {
        return self::send(
            $recipient,
            'reclamation_deleted',
            "Votre réclamation #{$reclamationId} a été supprimée.",
            null,
            ['reclamation_id' => $reclamationId]
        );
    }
}