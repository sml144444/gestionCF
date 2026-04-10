<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discipline extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'id_user', 'total_absence_heures',
        'total_retard_minutes', 'note_discipline',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}