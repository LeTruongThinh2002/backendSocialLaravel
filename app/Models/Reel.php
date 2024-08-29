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
        return $this->belongsTo(User::class);
    }
    public function reelsLike()
    {
        return $this->hasMany('\App\Models\ReelsLike', 'reels_id');
    }
    public function reelsComments()
    {
        return $this->hasMany(ReelsComment::class);
    }
}
