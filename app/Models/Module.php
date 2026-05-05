<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'id_filiere',
        'id_option',
        'name',
        'coefficience',
        'nbr_heure',
        'nbr_controles',
        'id_user',
        'id_user_remplacant',
        'type',
        'annee',
    ];

    // ── Relations ────────────────────────────────────────────

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }

    public function formateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function remplacant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user_remplacant');
    }

    public function emploisDuTemps(): HasMany
    {
        return $this->hasMany(EmploiDuTemps::class, 'id_module');
    }

    public function controles(): HasMany
    {
        return $this->hasMany(Controle::class, 'id_module');
    }

    /**
     * Full replacement/assignment audit trail, newest first.
     */
    public function formateurHistory(): HasMany
    {
        return $this->hasMany(ModuleFormateurHistory::class, 'module_id')
                    ->orderBy('start_date', 'desc');
    }

    // ── Helpers ──────────────────────────────────────────────

    /**
     * Returns the currently active replacement record, or null if none.
     */
    public function activeReplacement(): ?ModuleFormateurHistory
    {
        return $this->formateurHistory()
                    ->where('type', 'remplacement')
                    ->where('is_active', true)
                    ->first();
    }

    /**
     * True if the module currently has an active replacement formateur.
     */
    public function hasActiveReplacement(): bool
    {
        return $this->activeReplacement() !== null;
    }
}