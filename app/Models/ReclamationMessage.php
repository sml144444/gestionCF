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
}