<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceRetard extends Model
{
    protected $table = 'absence_retard';

    protected $fillable = [
        'id_user',
        'id_cours',
        'type',
        'session_part',
        'duree',
        'justifie',
        'file_justification',
        // ✅ NEW: admin approved without requiring a justification document.
        // justifie stays false — the absence is recorded as non-justified —
        // but this flag suppresses the "last unjustified absence" warning
        // shown to formateurs on the séance sheet.
        'admin_validated',
        'date_event',
    ];

    protected $casts = [
        'justifie'        => 'boolean',
        'admin_validated' => 'boolean', // ✅ NEW
        'date_event'      => 'datetime',
    ];

    // ── RELATIONSHIPS ──────────────────────────────────────────

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours');
    }
}