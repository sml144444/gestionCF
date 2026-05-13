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
     */
    public static function send(
        User    $recipient,
        string  $type,
        string  $message,
        ?string $url  = null,
        array   $data = []
    ): UserNotification {

        $existing = self::findGroupable($recipient->id, $type, $data);

        if ($existing) {
            return self::increment($existing, $data);
        }

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

    // ── Private: find a groupable unread notification ─────────
    private static function findGroupable(int $userId, string $type, array $data): ?UserNotification
    {
        $groupableTypes = ['reclamation_reply', 'reportation_reply'];

        if (! in_array($type, $groupableTypes)) {
            return null;
        }

        $entityId = $data['reclamation_id'] ?? $data['reportation_id'] ?? null;
        if (! $entityId) {
            return null;
        }

        return UserNotification::forUser($userId)
            ->unread()
            ->where('type', $type)
            ->where(function ($q) use ($data) {
                if (isset($data['reclamation_id'])) {
                    $q->whereJsonContains('data->reclamation_id', $data['reclamation_id']);
                } elseif (isset($data['reportation_id'])) {
                    $q->whereJsonContains('data->reportation_id', $data['reportation_id']);
                }
            })
            ->latest()
            ->first();
    }

    private static function increment(UserNotification $notification, array $data): UserNotification
    {
        $newCount = $notification->count + 1;

        $reclamationId = $data['reclamation_id']  ?? ($notification->data['reclamation_id']  ?? null);
        $reportationId = $data['reportation_id']  ?? ($notification->data['reportation_id']  ?? null);

        if ($reportationId) {
            $message = "{$newCount} nouveaux messages dans la reportation #{$reportationId}.";
        } elseif ($reclamationId) {
            $message = "{$newCount} nouveaux messages dans la réclamation #{$reclamationId}.";
        } else {
            $message = "{$newCount} nouveaux messages.";
        }

        $notification->update([
            'count'   => $newCount,
            'message' => $message,
        ]);

        broadcast(new NotificationUpdated($notification->fresh()));

        return $notification;
    }

    // ── Convenience wrappers ───────────────────────────────────

    public static function reclamationReply(User $recipient, int $reclamationId, string $senderName, string $url): UserNotification
    {
        $existing = self::findGroupable($recipient->id, 'reclamation_reply', ['reclamation_id' => $reclamationId]);

        $message = $existing
            ? ($existing->count + 1) . " nouveaux messages dans la réclamation #{$reclamationId}."
            : "{$senderName} a répondu à la réclamation #{$reclamationId}.";

        return self::send($recipient, 'reclamation_reply', $message, $url, ['reclamation_id' => $reclamationId]);
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

    public static function noteAdded(User $recipient, string $moduleName, string $url): UserNotification
    {
        return self::send(
            $recipient,
            'note',
            "Vos notes pour le module \"{$moduleName}\" ont été mises à jour.",
            $url,
            ['module_name' => $moduleName]
        );
    }

    public static function absenceRecorded(User $recipient, string $moduleName, string $url): UserNotification
    {
        return self::send(
            $recipient,
            'absence',
            "Une absence a été enregistrée pour le module \"{$moduleName}\". Vérifiez et soumettez votre justificatif.",
            $url,
            ['module_name' => $moduleName]
        );
    }

    public static function ressourceAdded(User $recipient, string $titre, string $moduleName, string $url): UserNotification
    {
        return self::send(
            $recipient,
            'ressource',
            "Nouvelle ressource « {$titre} » ajoutée pour le module \"{$moduleName}\".",
            $url,
            ['module_name' => $moduleName]
        );
    }

    // ══════════════════════════════════════════════════════════════
    // ✅ NEW — Notify all admins when a stagiaire submits a
    //          justification file for one or more absences.
    //
    //  $stagiaire   — the User who uploaded the file
    //  $moduleName  — first module name from the absence(s), or 'N/A'
    //  $url         — link to the absences index (admin view)
    //  $absenceIds  — array of AbsenceRetard IDs covered by the file
    // ══════════════════════════════════════════════════════════════
    public static function justificationSoumise(
        User   $stagiaire,
        string $moduleName,
        string $url,
        array  $absenceIds = []
    ): void {
        // Find every user who can validate justifications.
        // Adjust the role list if your permission system differs.
        $admins = User::whereIn('role', ['admin', 'gestionnaire'])->get();

        foreach ($admins as $admin) {
            self::send(
                $admin,
                'absence_justification',
                "{$stagiaire->name} a soumis un justificatif pour le module \"{$moduleName}\".",
                $url,
                [
                    'stagiaire_id' => $stagiaire->id,
                    'absence_ids'  => $absenceIds,
                    'module_name'  => $moduleName,
                ]
            );
        }
    }

    // ── Notify stagiaire: admin authorised absence without justification ──
public static function absenceAutorisee(
    User   $stagiaire,
    string $date,
    string $url
): UserNotification {
    return self::send(
        $stagiaire,
        'absence_autorisee',
        "Votre absence du {$date} a été autorisée sans justificatif par l'administration.",
        $url,
        ['date' => $date]
    );
}

// ── Notify stagiaire: justification accepted ──────────────────────────
public static function justificationAcceptee(
    User   $stagiaire,
    string $date,
    string $moduleName,
    string $url
): UserNotification {
    return self::send(
        $stagiaire,
        'absence_justification_accepted',
        "Votre justificatif pour l'absence du {$date} (« {$moduleName} ») a été accepté.",
        $url,
        ['date' => $date, 'module_name' => $moduleName]
    );
}

// ── Notify stagiaire: justification refused ───────────────────────────
public static function justificationRefusee(
    User   $stagiaire,
    string $date,
    string $moduleName,
    string $url
): UserNotification {
    return self::send(
        $stagiaire,
        'absence_justification_refused',
        "Votre justificatif pour l'absence du {$date} (« {$moduleName} ») a été refusé. Vous pouvez en soumettre un nouveau.",
        $url,
        ['date' => $date, 'module_name' => $moduleName]
    );
}
}