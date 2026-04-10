<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salle extends Model
{
    protected $fillable = ['name', 'capacity'];

    public function emploisDuTemps()
    {
        return $this->hasMany(EmploiDuTemps::class, 'id_salle');
    }
}