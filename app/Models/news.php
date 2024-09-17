<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class news extends Model
{
    use HasFactory;
    protected $table = 'news';
    protected $fillable = [
        'user_id',
        'description',
        'media',
    ];
    public function newsUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

}
