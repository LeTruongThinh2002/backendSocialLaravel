<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reel extends Model
{
    use HasFactory;
    protected $table = 'reels';
    public function reelsUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function reelsLike()
    {
        return $this->belongsToMany(User::class, 'reels_like', 'reels_id', 'user_id');
    }
    public function reelsComment()
    {
        return $this->hasMany(ReelsComment::class);
    }
}
