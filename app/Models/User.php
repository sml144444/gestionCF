<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @mixin \Spatie\Permission\Traits\HasRoles
 * @mixin \Spatie\Permission\Traits\HasPermissions
 */
class User extends Authenticatable implements CanResetPasswordContract
{
    use Notifiable;
    use HasRoles;
    use CanResetPassword;

    protected $guard_name = 'web';

    protected $fillable = [
        'name', 'email', 'password', 'cin', 'phone',
        'date_embauche', 'matricule_formateur', 'specialite', 'nbr_heure_limit',
        'document', 'photo', 'date_naissance', 'role',
        'id_filiere', 'id_option', 'id_groupe',
    ];

    protected $hidden = ['password'];

    protected $casts = [
        'document'       => 'array',
        'date_naissance' => 'date',
        'date_embauche'  => 'date',
    ];

    // --- Relations communes ---

    public function filiere()
    {
        return $this->belongsTo(Filiere::class, 'id_filiere');
    }

    public function option()
    {
        return $this->belongsTo(Option::class, 'id_option');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'id_groupe');
    }

    // --- Formateur ---

    public function modules()
    {
        return $this->hasMany(Module::class, 'id_user');
    }

    public function coursCreated()
    {
        return $this->hasMany(Cours::class, 'created_by');
    }

    public function controlesCreated()
    {
        return $this->hasMany(Controle::class, 'created_by');
    }

    public function reportations()
    {
        return $this->hasMany(Reportation::class, 'id_user');
    }

    public function emploisGeres()
    {
        return $this->hasMany(EmploiDuTemps::class, 'id_user');
    }

    public function reportationsValidees()
    {
        return $this->hasMany(Reportation::class, 'valide_by');
    }

    // --- Stagiaire ---

    public function absences()
    {
        return $this->hasMany(AbsenceRetard::class, 'id_user');
    }

    public function notes()
    {
        return $this->hasMany(Note::class, 'id_user');
    }

    public function discipline()
    {
        return $this->hasOne(Discipline::class, 'id_user');
    }

    public function effs()
    {
        return $this->hasMany(Eff::class, 'id_user');
    }

    public function bulletins()
    {
        return $this->hasMany(Bulletin::class, 'id_user');
    }

    public function reclamations()
    {
        return $this->hasMany(Reclamation::class, 'id_user');
    }

    // --- Admin/Gestionnaire ---

    public function newsEvents()
    {
        return $this->hasMany(NewsEvent::class, 'id_user');
    }

    // --- Helpers rôle ---

    public function isAdmin(): bool        { return $this->role === 'admin'; }
    public function isGestionnaire(): bool { return $this->role === 'gestionnaire'; }
    public function isFormateur(): bool    { return $this->role === 'formateur'; }
    public function isStagiaire(): bool    { return $this->role === 'stagiaire'; }
}