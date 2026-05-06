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
        'count',   // ← ajouter ça
    ];

protected $casts = [
    'data'    => 'array',
    'read_at' => 'datetime',
    'count'   => 'integer',  // ← correct
];

    // ── Notification type → icon / color mapping (used in blade) ─
    public const TYPES = [
        'reclamation_reply'    => ['icon' => '💬', 'color' => '#2563eb', 'bg' => '#eff6ff'],
        'reclamation_assigned' => ['icon' => '📋', 'color' => '#7c3aed', 'bg' => '#f5f3ff'],
        'reclamation_status'   => ['icon' => '🔄', 'color' => '#059669', 'bg' => '#ecfdf5'],
        'reclamation_deleted'  => ['icon' => '🗑️', 'color' => '#dc2626', 'bg' => '#fef2f2'],
        'note'                 => ['icon' => '📝', 'color' => '#d97706', 'bg' => '#fffbeb'],
        'default'              => ['icon' => '🔔', 'color' => '#64748b', 'bg' => '#f8fafc'],
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