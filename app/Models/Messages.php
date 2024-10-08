<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Messages extends Model
{
    use HasFactory;

    protected $table = 'messages';


    public function messagesMedia()
    {
        return $this->hasMany('App\Models\MessagesMedia', 'message_id');
    }
    public function messagesUser()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
