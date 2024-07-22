<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostBackground extends Model
{
    use HasFactory;
    protected $table = 'posts_background';
    public function Receipt()
    {
        return $this->hasMany(ReceiptPostsBackground::class, 'posts_background_id');
    }
    public function UserUsing()
    {
        return $this->hasMany(User::class, 'users_posts_background');
    }
}
