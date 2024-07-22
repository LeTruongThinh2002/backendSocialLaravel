<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $table = 'posts_comments';

    public function UserLike()
    {
        return $this->hasMany('App\Models\CommentsLikes');
    }
}
