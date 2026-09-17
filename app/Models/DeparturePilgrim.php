<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeparturePilgrim extends Model
{
    protected $fillable = ['departure_id', 'pilgrim_id', 'room_number', 'bus_number', 'manifest_status'];

    public function departure() { return $this->belongsTo(Departure::class); }
    public function pilgrim() { return $this->belongsTo(Pilgrim::class); }
}
