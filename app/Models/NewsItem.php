<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsItem extends Model
{
    protected $fillable = [
        'country_id', 'title', 'url', 'source', 'category', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}