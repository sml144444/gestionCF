<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = ['id_user', 'id_controle', 'note'];

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function controle()
    {
        return $this->belongsTo(Controle::class, 'id_controle');
    }
}