<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bulletin extends Model
{
    protected $table = 'bulletin';

    protected $fillable = [
        'id_user', 'moyenne_generale',
        'note_discipline', 'note_eff', 'note_finale', 'annee',
    ];

    public function stagiaire()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}