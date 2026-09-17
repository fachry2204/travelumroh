<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsPage extends Model
{
    protected $fillable = [
        'type', 'title', 'content', 'image_path', 'meta_title', 'meta_description', 'sort_order', 'status',
    ];
}
