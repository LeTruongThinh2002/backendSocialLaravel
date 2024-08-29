<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;
    protected $table = 'chats';
    public function ownerUser()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function memberUser()
    {
        return $this->belongsToMany(User::class, 'chats_member', 'chat_id', 'user_id');
    }
    public function messages()
    {
        return $this->hasMany(Messages::class);
    }
}
