<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'role_type', 'status',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function agent()
    {
        return $this->hasOne(Agent::class);
    }

    public function representative()
    {
        return $this->hasOne(Representative::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function isAdmin(): bool
    {
        return $this->role_type === 'admin';
    }

    public function isMember(): bool
    {
        return $this->role_type === 'member';
    }

    public function isAgent(): bool
    {
        return $this->role_type === 'agent';
    }

    public function isRepresentative(): bool
    {
        return $this->role_type === 'representative';
    }
}
