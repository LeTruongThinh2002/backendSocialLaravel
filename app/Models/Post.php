<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    public function postsUser()
    {
        return $this->belongsTo(User::class);
    }
    public function postsMedia()
    {
        return $this->hasMany('App\Models\PostsMedia', 'post_id');
    }

    public function postsComment()
    {
        return $this->hasMany('App\Models\PostsComment', 'post_id');
    }

    public function postsLike()
    {
        return $this->hasMany('App\Models\PostsLike', 'post_id');
    }
}
