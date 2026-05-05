<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmploiDuTemps extends Model
{
    protected $table = 'emplois_du_temps';

    protected $fillable = [
        'id_module',
        'id_groupe',
        'id_salle',
        'date_debut',
        'date_fin',
        'id_user',
        'id_user_remplacant',
        'jour',
        'statut',
        'mode',
        'lien_distance',
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
    ];

    // ── Relations ────────────────────────────────────────────

    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class, 'id_module');
    }

    public function groupe(): BelongsTo
    {
        return $this->belongsTo(Groupe::class, 'id_groupe');
    }

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class, 'id_salle');
    }

    /**
     * The original assigned formateur (id_user — never changes after creation).
     */
    public function gestionnaire(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    /**
     * The replacement formateur recorded on this specific session.
     * This is set when activateReplacement() propagates to future sessions.
     * It is NEVER cleared retroactively — history is permanent.
     */
    public function remplacant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_remplacant');
    }

    public function cours(): HasMany
    {
        return $this->hasMany(Cours::class, 'id_emplois_du_temps');
    }

    public function reportations(): HasMany
    {
        return $this->hasMany(Reportation::class, 'id_emplois_du_temps');
    }

    // ── Helpers ──────────────────────────────────────────────

    public function isDistance(): bool
    {
        return $this->mode === 'distance';
    }

    /**
     * The formateur who actually teaches this session.
     * If a replacement was recorded at the time the session was scheduled,
     * it is returned. Otherwise the original formateur is returned.
     *
     * NOTE: This reads purely from the session record — no inference from
     * the module's current state. History is never lost this way.
     */
    public function formateurActif(): ?User
    {
        return $this->id_user_remplacant
            ? $this->remplacant
            : $this->gestionnaire;
    }

    /**
     * True if this session has a recorded replacement formateur.
     */
    public function hasRemplacant(): bool
    {
        return (bool) $this->id_user_remplacant;
    }
}