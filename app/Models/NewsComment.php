<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsComment extends Model
{
    protected $table = 'news_comments';

    protected $fillable = ['news_event_id', 'user_id', 'contenu'];

    public function auteur()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function news()
    {
        return $this->belongsTo(NewsEvent::class, 'news_event_id');
    }
}