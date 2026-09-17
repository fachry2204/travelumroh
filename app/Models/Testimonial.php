<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'photo', 'package_name', 'content', 'rating', 'sort_order', 'status'];
}
