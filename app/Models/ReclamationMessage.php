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
    'attachment_path',   // ← new
    'attachment_name',   // ← new
    'attachment_mime',   // ← new
    ];
    

    protected $casts = [
        'seen_at'   => 'datetime',
        'edited_at' => 'datetime',
    ];

    public function getIsImageAttribute(): bool
{
    return $this->attachment_mime && str_starts_with($this->attachment_mime, 'image/');
}

    // ── RELATIONS ─────────────────────────────────────────────
    public function reclamation(): BelongsTo
    {
        return $this->belongsTo(Reclamation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /** Edit only if: I sent it + not seen + no attachment */
public function canEdit(User $user): bool
{
    return $this->sender_id === $user->id
        && is_null($this->seen_at)
        && is_null($this->attachment_path);
}
    // ── HELPERS ───────────────────────────────────────────────

    /** Can edit/delete only if I sent it AND the other party hasn't seen it yet */
    public function canEditOrDelete(User $user): bool
    {
        return $this->sender_id === $user->id && is_null($this->seen_at);
    }
}