<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Edu extends Model
{
    protected $table = 'edu';
    protected $fillable = [
        'edu_email',
        'password',
        'nom',
        'prenom',
        'filiere_code',
        'groupe_code',
        'used',
    ];

    protected $hidden = ['password'];

    /** Resolve the matching Filiere using filiere_code */
    public function filiere()
    {
        return $this->hasOne(Filiere::class, 'code', 'filiere_code');
    }

    /** Resolve the matching Groupe using groupe_code */
    public function groupe()
    {
        return $this->hasOne(Groupe::class, 'code', 'groupe_code');
    }

    /** Full name helper */
    public function getFullNameAttribute(): string
    {
        return trim($this->nom . ' ' . $this->prenom);
    }
}