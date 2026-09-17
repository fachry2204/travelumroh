<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'content', 'featured_image', 'category',
        'status', 'created_by', 'published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
            if ($article->status === 'published' && empty($article->published_at)) {
                $article->published_at = now();
            }
        });
    }

    public function author() { return $this->belongsTo(User::class, 'created_by'); }
}
