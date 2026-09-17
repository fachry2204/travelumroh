<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Traits\RecordsAuditLog;

class Payment extends Model
{
    use RecordsAuditLog;

    protected $fillable = [
        'booking_id', 'payment_number', 'payment_type', 'amount', 'method',
        'proof_file', 'bank_name', 'account_name', 'account_number',
        'status', 'validated_by', 'validated_at', 'note',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'validated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($payment) {
            if (empty($payment->payment_number)) {
                $payment->payment_number = 'PAY' . date('Ymd') . strtoupper(Str::random(6));
            }
        });

        static::updated(function ($payment) {
            if ($payment->status === 'approved') {
                $booking = $payment->booking;
                $totalPaid = $booking->payments()->where('status', 'approved')->sum('amount');
                $booking->paid_amount = $totalPaid;
                $booking->outstanding_amount = $booking->total_amount - $totalPaid;
                if ($booking->outstanding_amount <= 0) {
                    $booking->booking_status = 'paid';
                } elseif ($totalPaid > 0) {
                    $booking->booking_status = 'dp';
                }
                $booking->save();

                // Approve commission if exists
                if ($booking->commission && $booking->commission->status === 'pending') {
                    $booking->commission->update(['status' => 'approved']);
                }
            }
        });
    }

    public function booking() { return $this->belongsTo(Booking::class); }
    public function validator() { return $this->belongsTo(User::class, 'validated_by'); }
}
