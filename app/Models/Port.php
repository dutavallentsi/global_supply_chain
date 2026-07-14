<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Port extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id', 'name', 'unlocode', 'latitude', 'longitude', 'type',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function weatherSnapshots(): HasMany
    {
        return $this->hasMany(WeatherSnapshot::class);
    }

    public function latestWeather()
    {
        return $this->hasOne(WeatherSnapshot::class)->latestOfMany('recorded_at');
    }
}