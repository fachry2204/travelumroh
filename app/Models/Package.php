<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Package extends Model
{
    protected $fillable = [
        'code', 'name', 'slug', 'departure_date', 'return_date', 'duration_days',
        'airline', 'departure_airport', 'makkah_hotel', 'madinah_hotel',
        'price_quad', 'price_triple', 'price_double', 'minimum_dp',
        'quota', 'remaining_seat', 'facilities', 'excluded', 'description',
        'featured_image', 'status',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'return_date' => 'date',
        'price_quad' => 'decimal:2',
        'price_triple' => 'decimal:2',
        'price_double' => 'decimal:2',
        'minimum_dp' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($package) {
            if (empty($package->slug)) {
                $package->slug = Str::slug($package->name . '-' . $package->code);
            }
        });
    }

    public function itineraries() { return $this->hasMany(PackageItinerary::class)->orderBy('day_number'); }
    public function bookings() { return $this->hasMany(Booking::class); }
    public function departures() { return $this->hasMany(Departure::class); }

    public function getMinPriceAttribute()
    {
        $prices = array_filter([$this->price_quad, $this->price_triple, $this->price_double]);
        return $prices ? min($prices) : 0;
    }
}
