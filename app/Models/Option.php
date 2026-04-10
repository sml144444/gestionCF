<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    protected $fillable = ['id_filiere', 'titre'];

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }

    public function groupes()
    {
        return $this->hasMany(Groupe::class, 'id_option');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'id_option');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'id_option');
    }
}