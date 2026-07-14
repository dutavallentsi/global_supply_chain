<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WeatherSnapshot extends Model
{
    protected $fillable = [
        'port_id', 'latitude', 'longitude', 'temperature_c',
        'precipitation_mm', 'wind_speed_kmh', 'storm_risk_level', 'recorded_at',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
    ];

    public function port(): BelongsTo
    {
        return $this->belongsTo(Port::class);
    }
}