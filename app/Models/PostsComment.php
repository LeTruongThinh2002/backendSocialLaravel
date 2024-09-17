<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostsComment extends Model
{
    use HasFactory;
    protected $table = 'posts_comment';
    protected $fillable = [
        'user_id',
        'post_id',
        'parent_comment_id',
        'comment',
    ];


    public function commentUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function commentInPosts()
    {
        return $this->belongsTo(Post::class, 'post_id', 'id');
    }

    public function commentReply()
    {
        return $this->hasMany(PostsComment::class, 'parent_comment_id', 'id');
    }

    public function commentLike()
    {
        return $this->belongsToMany(User::class, 'posts_comment_like', 'comment_id', 'user_id');
    }
}
