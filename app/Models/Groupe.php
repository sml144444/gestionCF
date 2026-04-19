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
    'promo',   // ← year the cohort STARTED, e.g. 2024
    'name',
    'code',
];

/**
 * Returns "2024–2026" using filière duration.
 * Call: $groupe->promo_label
 */
public function getPromoLabelAttribute(): string
{
    if (! $this->promo) return '—';

    $duree = $this->filiere?->duree ?? 2;   // fallback 2 years
    $end   = $this->promo + $duree;

    return $this->promo . '–' . $end;
}

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'id_option');
    }

    public function stagiaires()
    {
        return $this->hasMany(User::class, 'id_groupe');
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