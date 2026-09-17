<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $fillable = [
        'booking_id', 'agent_id', 'commission_type', 'commission_value',
        'commission_amount', 'status', 'paid_at', 'note',
    ];

    protected $casts = [
        'commission_value' => 'decimal:2',
        'commission_amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function agent() { return $this->belongsTo(Agent::class); }
}
