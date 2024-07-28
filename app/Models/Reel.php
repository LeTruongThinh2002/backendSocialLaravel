<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;
    protected $table = 'reels';
    public function UserLike()
    {
        return $this->belongsToMany(User::class, 'reels_likes');
    }
    public function Author()
    {
        return $this->belongsTo(User::class, 'users_reels');
    }
    public function Comments()
    {
        return $this->hasMany(ReelComment::class);
    }
}
