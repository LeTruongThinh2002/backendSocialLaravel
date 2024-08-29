<?php

namespace App\Models;

// use App\Notifications\VerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Notifications\VerifyEmail;
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

    public function userFollow()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_id', 'user_following')->pluck('user_following');
    }

    public function userFollower()
    {
        return $this->belongsToMany(User::class, 'user_follow', 'user_following', 'user_id')->pluck('user_id');
    }

    public function friends()
    {
        $followings = $this->userFollow();
        return $this->userFollower()->whereIn('user_id', $followings)->pluck('user_id');
    }

    public function userBlock()
    {
        return $this->belongsToMany(User::class, 'user_block', 'user_id', 'user_blocked')->pluck('user_blocked');
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
