<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\RecordsAuditLog;

class Booking extends Model
{
    use HasFactory, RecordsAuditLog;

    protected $fillable = [
        'booking_number', 'user_id', 'package_id', 'agent_id', 'representative_id',
        'referral_code', 'source', 'total_pilgrims', 'room_type',
        'total_amount', 'paid_amount', 'outstanding_amount',
        'booking_status', 'document_status', 'visa_status', 'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'outstanding_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($booking) {
            if (empty($booking->booking_number)) {
                $booking->booking_number = 'BK' . date('Ymd') . strtoupper(Str::random(6));
            }
            // Calculate outstanding
            $booking->outstanding_amount = $booking->total_amount - $booking->paid_amount;
        });
    }

    public function user() { return $this->belongsTo(User::class); }
    public function package() { return $this->belongsTo(Package::class); }
    public function agent() { return $this->belongsTo(Agent::class); }
    public function representative() { return $this->belongsTo(Representative::class); }
    public function pilgrims() { return $this->hasMany(Pilgrim::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function commission() { return $this->hasOne(Commission::class); }
}
