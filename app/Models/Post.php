<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    public function UserLike()
    {
        return $this->belongsToMany(User::class, 'posts_likes');
    }
    public function Author()
    {
        return $this->belongsTo(User::class, 'users_posts');
    }
    public function Comments()
    {
        return $this->hasMany(Comment::class);
    }
}
