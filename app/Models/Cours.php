<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    protected $table = 'cours';

    protected $fillable = [
        'id_emplois_du_temps', 'titre', 'description',
        'fichier', 'lien', 'remarque', 'statut', 'created_by',
    ];

    protected $casts = ['fichier' => 'array'];

    public function emploi(): \Illuminate\Database\Eloquent\Relations\BelongsTo
{
    return $this->belongsTo(\App\Models\EmploiDuTemps::class, 'id_emplois_du_temps');
}
    public function emploiDuTemps()
    {
        return $this->belongsTo(EmploiDuTemps::class, 'id_emplois_du_temps');
    }

    public function formateur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function absences()
    {
        return $this->hasMany(AbsenceRetard::class, 'id_cours');
    }
}