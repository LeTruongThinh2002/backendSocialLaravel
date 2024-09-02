<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $table = 'posts';
    protected $fillable = [
        'user_id',
        'description',
    ];
    public function postsUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function postsMedia()
    {
        return $this->hasMany(PostsMedia::class, 'post_id');
    }

    public function postsComment()
    {
        return $this->hasMany(PostsComment::class, 'post_id');
    }

    public function postsLike()
    {
        return $this->belongsToMany(User::class, 'posts_like', 'post_id', 'user_id');
    }
}
