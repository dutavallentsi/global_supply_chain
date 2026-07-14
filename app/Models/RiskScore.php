<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    protected $fillable = [
        'shipment_id', 'weather_risk', 'port_congestion_risk',
        'geopolitical_risk', 'currency_risk', 'inflation_risk',
        'total_risk_score', 'risk_level', 'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
    ];

    public function shipment(): BelongsTo
    {
        return $this->belongsTo(Shipment::class);
    }
}