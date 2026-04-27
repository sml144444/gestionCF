<?php
// ─────────────────────────────────────────────────────────────────────────────
// In App\Models\Module — add 'nbr_controles' to $fillable and add relation:
// ─────────────────────────────────────────────────────────────────────────────

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'id_filiere',
        'id_option',
        'name',
        'coefficience',
        'nbr_heure',
        'nbr_controles',        // ← ADD THIS
        'id_user',
        'id_user_remplacant',
        'type',
        'annee',
    ];

    // ─── Relations ─────────────────────────────────────────────────────────

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }

    public function formateur()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function remplacant()
    {
        return $this->belongsTo(User::class, 'id_user_remplacant');
    }

    public function emploisDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class, 'id_module');
    }

    public function controles()          // ← ADD THIS
    {
        return $this->hasMany(Controle::class, 'id_module');
    }
}