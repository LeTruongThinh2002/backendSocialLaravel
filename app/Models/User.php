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
}
