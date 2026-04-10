<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Controle extends Model
{
    protected $fillable = [
        'titre', 'id_module', 'id_groupe',
        'type', 'duree', 'description', 'variante', 'created_by',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'id_groupe');
    }

    public function formateur()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'id_controle');
    }
}