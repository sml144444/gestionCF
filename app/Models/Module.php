<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
protected $fillable = [
    'id_filiere', 'id_option', 'name',
    'coefficience', 'nbr_heure', 'id_user',
    'id_user_remplacant',   // ← ajouter
    'type', 'annee',
];

public function remplacant()
{
    return $this->belongsTo(User::class, 'id_user_remplacant');
}

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'id_option');
    }

    public function formateur()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function emploisDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class, 'id_module');
    }

    public function controles()
    {
        return $this->hasMany(Controle::class, 'id_module');
    }

        public function progresGroupes()
    {
        return $this->hasMany(ModuleGroupeProgress::class, 'id_module');
    }
}