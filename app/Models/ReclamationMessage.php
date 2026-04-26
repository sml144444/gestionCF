<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReclamationMessage extends Model
{
    protected $fillable = [
        'reclamation_id',
        'sender_id',
        'message',
        'seen_at',
        'edited_at',
    ];

    protected $casts = [
        'seen_at'   => 'datetime',
        'edited_at' => 'datetime',
    ];

    // ── RELATIONS ─────────────────────────────────────────────
    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(Reclamation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ── HELPERS ───────────────────────────────────────────────

    /** Can edit/delete only if I sent it AND the other party hasn't seen it yet */
    public function canEditOrDelete(User $user): bool
    {
        return $this->sender_id === $user->id && is_null($this->seen_at);
    }
}