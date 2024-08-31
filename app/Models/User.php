<?php

namespace App\Models;

// use App\Notifications\VerifyEmail;

use App\Notifications\ResetPassword;
use App\Notifications\VerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use HasFactory, Notifiable;
    protected $table = 'users';
    public $timestamps = true;

    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'email',
        'password',
        'email_verified_at',
        'date_of_birth',
        'country',
        'avatar',
        'background'
    ];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail);
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    public function userFollow()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'user_following');
    }

    public function userFollower()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_following');
    }

    public function userBlock()
    {
        return $this->belongsToMany(User::class, 'user_block', 'user_id', 'user_blocked');
    }



    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function news()
    {
        return $this->hasMany(news::class);
    }

    public function reels()
    {
        return $this->hasMany(Reel::class);
    }

}
