<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EduImportLog extends Model
{
    protected $fillable = [
        'id_user', 'filename', 'imported', 'skipped', 'errors',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function eduAccounts()
{
    return $this->hasMany(Edu::class, 'edu_import_log_id');
}
}