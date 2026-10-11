<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    protected $fillable = [
        'name',
        'email',
        'password',
        'no_hp',
        'lokasi',
        'deskripsi_user',
        'foto_profile',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
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

    public function mobils()
    {
        return $this->hasMany(\App\Models\Mobil::class);
    }
    
    public function likedMobils()
    {
        return $this->belongsToMany(\App\Models\Mobil::class, 'mobil_likes', 'user_id', 'mobil_id');
    }

     public function likedMotors()
    {
        return $this->belongsToMany(\App\Models\Motor::class, 'motor_likes', 'user_id', 'motor_id');
    }

}
