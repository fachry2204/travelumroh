<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departure extends Model
{
    protected $fillable = [
        'package_id', 'group_code', 'departure_date', 'return_date',
        'guide_name', 'airline', 'flight_number_departure', 'flight_number_return', 'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
    ];

    public function package() { return $this->belongsTo(Package::class); }
    public function pilgrims() { return $this->hasMany(DeparturePilgrim::class); }
}
