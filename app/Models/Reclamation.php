<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    protected $fillable = ['id_user', 'type', 'description', 'status'];

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}