<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groupe extends Model
{
    protected $fillable = [
        'id_filiere',
        'id_option',
        'nbr_limit',
        'annee',   // ← 1 = première année, 2 = deuxième année (2 ans ou 2.5 ans)
        'name', 
        'code'   // ← optional: human readable name like "G1A"
    ];

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