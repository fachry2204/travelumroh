<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'avatar', 'address', 'province', 'city', 'district', 'village', 'postal_code', 'identity_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
