<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reportation extends Model
{
protected $fillable = [
    'id_emplois_du_temps', 'id_user',
    'nouvelle_date_debut', 'nouvelle_date_fin',
    'raison', 'status', 'valide_by', 'assigned_to',
];

    protected $casts = [
        'nouvelle_date_debut' => 'datetime',
        'nouvelle_date_fin'   => 'datetime',
    ];

    public function emploiDuTemps()
    {
        return $this->belongsTo(EmploiDuTemps::class, 'id_emplois_du_temps');
    }

    public function formateur()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_by');
    }

    public function assignedTo()
{
    return $this->belongsTo(User::class, 'assigned_to');
}

public function messages()
{
    return $this->hasMany(ReportationMessage::class)->oldest();
}
}