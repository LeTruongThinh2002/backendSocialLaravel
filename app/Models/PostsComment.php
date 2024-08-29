<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostsComment extends Model
{
    use HasFactory;
    protected $table = 'posts_comment';


    public function commentUser()
    {
        return $this->belongsTo(User::class);
    }

    public function commentInPosts()
    {
        return $this->belongsTo(Post::class);
    }

    public function commentReply()
    {
        return $this->hasMany(PostsComment::class, 'parent_comment_id');
    }

    public function commentLike()
    {
        return $this->belongsToMany(User::class, 'posts_comment_like', 'comment_id', 'user_id');
    }
}
