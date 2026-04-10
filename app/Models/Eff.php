<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eff extends Model
{
    protected $table = 'eff';

    protected $fillable = ['id_user', 'id_filiere', 'note_eff'];

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }
}