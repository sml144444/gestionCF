<?php

use Illuminate\Support\Facades\Broadcast;
use App\Models\Reclamation;
use App\Models\User;

// ── Default user channel ──────────────────────────────────
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// ── Conversation d réclamation ────────────────────────────
Broadcast::channel('reclamation.{reclamationId}', function (User $user, int $reclamationId) {
    $reclamation = Reclamation::find($reclamationId);
    if (! $reclamation) return false;
    return $reclamation->isAccessibleBy($user);
});

// ── Channel privé par user (assignation + notifications) ──
Broadcast::channel('user.{userId}', function (User $user, int $userId) {
    return (int) $user->id === (int) $userId;
});

// ── Channel public — nouvelle réclamation (admin/gestionnaire) ──
// Public channel — machi khasso auth
Broadcast::channel('reclamations.admin', function () {
    return true;
});