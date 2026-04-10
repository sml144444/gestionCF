<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsEvent extends Model
{
    protected $table = 'news_events';

    protected $fillable = ['id_user', 'titre', 'contenu', 'image'];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}