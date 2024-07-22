<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $table = 'chats';
    public function ChatValue()
    {
        return $this->hasMany('App\Models\ChatValue');
    }
    public function ChatUser()
    {
        return $this->belongsToMany(User::class, 'users_chat');
    }
}
