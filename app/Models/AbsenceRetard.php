<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbsenceRetard extends Model
{
    protected $table = 'absence_retard';

    protected $fillable = [
        'id_user', 'id_cours', 'type',
        'duree', 'justifie', 'file_justification', 'date_event',
    ];

    protected $casts = [
        'justifie'   => 'boolean',
        'date_event' => 'datetime',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class, 'id_cours');
    }
}