<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmploiDuTemps extends Model
{
    protected $table = 'emplois_du_temps';

    protected $fillable = [
        'id_module',
        'id_groupe',
        'id_salle',
        'date_debut',
        'date_fin',
        'id_user',
        'jour',
        'statut',
        'mode',           // 'presentiel' | 'distance'
        'lien_distance',  // Teams/Zoom link for distance sessions
        'id_user_remplacant',   // ← ajouter cette ligne
    ];

    protected $casts = [
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'id_groupe');
    }

    public function salle()
    {
        return $this->belongsTo(Salle::class, 'id_salle');
    }

    public function gestionnaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function cours()
    {
        return $this->hasMany(Cours::class, 'id_emplois_du_temps');
    }

    public function reportations()
    {
        return $this->hasMany(Reportation::class, 'id_emplois_du_temps');
    }

    // Helper
    public function isDistance(): bool
    {
        return $this->mode === 'distance';
    }

 
    /**
     * The replacement formateur (null = no replacement, original teaches).
     */
    public function remplacant()
    {
        return $this->belongsTo(\App\Models\User::class, 'id_user_remplacant');
    }
 
    /**
     * The formateur who will actually teach this session
     * (replacement if assigned, original otherwise).
     */
    public function formateurActif(): ?\App\Models\User
    {
        return $this->remplacant ?? $this->gestionnaire;
    }
 
}