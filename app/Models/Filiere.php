<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Filiere extends Model
{
    protected $fillable = ['name', 'duree'];

    public function options()
    {
        return $this->hasMany(Option::class, 'id_filiere');
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'id_filiere');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'id_filiere');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_filiere');
    }

    public function effs()
    {
        return $this->hasMany(Eff::class, 'id_filiere');
    }
}