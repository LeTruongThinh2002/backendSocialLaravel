<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommentChatBackground extends Model
{
    use HasFactory;
    protected $table = 'comments_chats_background';
    public function Receipt()
    {
        return $this->hasMany(ReceiptCommentsChatsBackground::class, 'comments_chats_background_id');
    }
    public function UserUsing()
    {
        return $this->hasMany(User::class, 'users_comments_chats_background');
    }
}
