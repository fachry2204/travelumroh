<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pilgrim extends Model
{
    protected $fillable = [
        'booking_id', 'user_id', 'full_name', 'nik', 'family_card_number',
        'birth_place', 'birth_date', 'gender', 'marital_status', 'job', 'education',
        'address', 'province', 'city', 'district', 'village', 'postal_code',
        'phone', 'email', 'passport_number', 'passport_issued_place',
        'passport_issued_date', 'passport_expired_date', 'passport_name',
        'blood_type', 'medical_history', 'allergy', 'special_needs',
        'emergency_contact_name', 'emergency_contact_relation',
        'emergency_contact_phone', 'emergency_contact_address',
        'room_type', 'room_number', 'bus_number',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'passport_issued_date' => 'date',
        'passport_expired_date' => 'date',
    ];

    public function booking() { return $this->belongsTo(Booking::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function documents() { return $this->hasMany(PilgrimDocument::class); }
    public function departurePilgrims() { return $this->hasMany(DeparturePilgrim::class); }
}
