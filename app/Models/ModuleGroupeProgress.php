<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModuleGroupeProgress extends Model
{
    protected $table = 'module_groupe_progress';

    protected $fillable = ['id_module', 'id_groupe', 'status'];

    public function module()
    {
        return $this->belongsTo(Module::class, 'id_module');
    }

    public function groupe()
    {
        return $this->belongsTo(Groupe::class, 'id_groupe');
    }
}