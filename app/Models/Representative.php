<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Representative extends Model
{
    protected $fillable = ['user_id', 'region_name', 'office_address', 'status'];

    public function user() { return $this->belongsTo(User::class); }
    public function agents() { return $this->hasMany(Agent::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
}
