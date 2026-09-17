<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    protected $fillable = [
        'user_id', 'representative_id', 'agent_code', 'referral_code',
        'address', 'google_maps_url',
        'commission_type', 'commission_value', 'status',
    ];

    protected $casts = ['commission_value' => 'decimal:2'];

    public function user() { return $this->belongsTo(User::class); }
    public function representative() { return $this->belongsTo(Representative::class); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function commissions() { return $this->hasMany(Commission::class); }
}
