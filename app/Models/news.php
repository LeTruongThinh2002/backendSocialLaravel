<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;
    protected $table = 'news';
    public function UserLike()
    {
        return $this->hasMany(User::class, 'news_likes');
    }
    public function Author()
    {
        return $this->belongsTo(User::class, 'users_news');
    }
}
