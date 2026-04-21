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

    public function comments()
    {
        return $this->hasMany(NewsComment::class, 'news_event_id')->with('auteur')->orderByDesc('created_at');
    }

    public function likes()
    {
        return $this->hasMany(NewsLike::class, 'news_event_id');
    }

    public function isLikedBy(User $user): bool
    {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}