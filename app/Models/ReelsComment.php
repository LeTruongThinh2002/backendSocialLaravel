<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReelsComment extends Model
{
    use HasFactory;
    protected $table = 'reels_comment';

    public function commentUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function commentInReels()
    {
        return $this->belongsTo(Reel::class, 'reels_id', 'id');
    }

    public function commentReply()
    {
        return $this->hasMany(ReelsComment::class, 'parent_comment_id');
    }

    public function commentLike()
    {
        return $this->belongsToMany(User::class, 'reels_comment_like', 'comment_id', 'user_id');
    }
}
