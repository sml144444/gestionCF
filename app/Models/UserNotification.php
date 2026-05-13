<?php
// app/Models/UserNotification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

class UserNotification extends Model
{
    protected $table = 'user_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'url',
        'data',
        'read_at',
        'count',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
        'count'   => 'integer',
    ];

    // ── Notification type → icon / color mapping ──────────────
    public const TYPES = [
        'reclamation_reply'      => ['icon' => '💬', 'color' => '#2563eb', 'bg' => '#eff6ff'],
        'reclamation_assigned'   => ['icon' => '📋', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
        'reclamation_status'     => ['icon' => '🔄', 'color' => '#059669', 'bg' => '#ecfdf5'],
        'reclamation_deleted'    => ['icon' => '🗑️', 'color' => '#dc2626', 'bg' => '#fef2f2'],
        'reportation_reply'      => ['icon' => '📅', 'color' => '#0891b2', 'bg' => '#ecfeff'],
        'reportation_new'        => ['icon' => '🆕', 'color' => '#d97706', 'bg' => '#fffbeb'],
        'note'                   => ['icon' => '📝', 'color' => '#d97706', 'bg' => '#fffbeb'],
        'absence'                => ['icon' => '⚠️',  'color' => '#dc2626', 'bg' => '#fff1f2'],
        'ressource'              => ['icon' => '📎', 'color' => '#0369a1', 'bg' => '#f0f9ff'],

        // ✅ NEW — sent to admins when a stagiaire uploads a justification
        'absence_justification'  => ['icon' => '📄', 'color' => '#d97706', 'bg' => '#fffbeb'],
    'absence_autorisee'              => ['icon' => '🔓', 'color' => '#d97706', 'bg' => '#fffbeb'],
    'absence_justification_accepted' => ['icon' => '✅', 'color' => '#059669', 'bg' => '#ecfdf5'],
    'absence_justification_refused'  => ['icon' => '❌', 'color' => '#dc2626', 'bg' => '#fef2f2'],

        'default'                => ['icon' => '🔔', 'color' => '#64748b', 'bg' => '#f8fafc'],
    ];

    public function getTypeConfigAttribute(): array
    {
        return self::TYPES[$this->type] ?? self::TYPES['default'];
    }

    public function getIsReadAttribute(): bool
    {
        return ! is_null($this->read_at);
    }

    // ── Relations ─────────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ────────────────────────────────────────────────
    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }
}