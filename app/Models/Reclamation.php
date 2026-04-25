<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reclamation extends Model
{
    protected $fillable = [
        'id_user',
        'type',
        'description',
        'status',
        'assigned_to',
    ];

    // ── STATUS HELPERS ────────────────────────────────────────
    public const STATUSES = [
        'en_attente' => ['label' => 'En attente', 'icon' => '⏳', 'bg' => '#fef3c7', 'color' => '#92400e', 'border' => '#fde68a'],
        'en_cours'   => ['label' => 'En cours',   'icon' => '🔄', 'bg' => '#eff6ff', 'color' => '#1e40af', 'border' => '#bfdbfe'],
        'traite'     => ['label' => 'Traité',      'icon' => '✅', 'bg' => '#dcfce7', 'color' => '#166534', 'border' => '#bbf7d0'],
    ];

    public const TYPES = [
        'note'      => ['label' => 'Note',          'icon' => '📝'],
        'absence'   => ['label' => 'Absence',        'icon' => '📅'],
        'emploi'    => ['label' => 'Emploi du temps','icon' => '🗓️'],
        'formateur' => ['label' => 'Formateur',      'icon' => '👨‍🏫'],
        'autre'     => ['label' => 'Autre',           'icon' => '📌'],
    ];

    public function getStatusConfigAttribute(): array
    {
        return self::STATUSES[$this->status] ?? self::STATUSES['en_attente'];
    }

    public function getTypeConfigAttribute(): array
    {
        return self::TYPES[$this->type] ?? self::TYPES['autre'];
    }

    // ── RELATIONS ─────────────────────────────────────────────
    public function stagiaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ReclamationMessage::class)->orderBy('created_at');
    }

    // ── AUTHORIZATION HELPER ──────────────────────────────────
    public function isAccessibleBy(User $user): bool
    {
        return $user->can('reclamation-manage')
            || $this->id_user     === $user->id
            || $this->assigned_to === $user->id;
    }

    public function canReply(User $user): bool
    {
        return $this->isAccessibleBy($user) && $this->status !== 'traite';
    }
}