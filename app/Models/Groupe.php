<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    protected $fillable = [
        'id_filiere',
        'id_option',
        'nbr_limit',
        'annee',
        'promo',
        'name',
        'code',
    ];

    // ─────────────────────────────────────────────────────────────────────────
    // ACCESSORS
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Returns "2024–2026" using filière duration.
     * Usage: $groupe->promo_label
     */
    public function getPromoLabelAttribute(): string
    {
        if (! $this->promo) return '—';

        $duree = $this->filiere?->duree ?? 2;
        $end   = $this->promo + $duree;

        return $this->promo . '–' . $end;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // RELATIONS
    // ─────────────────────────────────────────────────────────────────────────

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'id_option');
    }

    /**
     * ✅ FIX — only count/return real stagiaires, not formateurs/admins
     *    who might also have id_groupe set.
     */
    public function stagiaires()
    {
        return $this->hasMany(User::class, 'id_groupe')
                    ->where('role', 'stagiaire');
    }

    public function emploisDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class, 'id_groupe');
    }

    public function controles()
    {
        return $this->hasMany(Controle::class, 'id_groupe');
    }

    public function edus()
    {
        return $this->hasMany(Edu::class, 'id_groupe');
    }

    public function progresModules()
    {
        return $this->hasMany(ModuleGroupeProgress::class, 'id_groupe');
    }
}