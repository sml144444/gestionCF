<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsLike extends Model
{
    protected $table = 'news_likes';

    protected $fillable = ['news_event_id', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function news()
    {
        return $this->belongsTo(NewsEvent::class, 'news_event_id');
    }
}