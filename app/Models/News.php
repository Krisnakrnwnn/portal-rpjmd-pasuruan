<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'category',
        'content',
        'image_url',
        'published_at',
        'is_published',
    ];

    // Scope: hanya berita publik
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published'  => 'boolean',
        ];
    }
}
