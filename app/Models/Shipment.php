<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'product_name', 'quantity',
        'origin_country_id', 'destination_country_id',
        'origin_port_id', 'destination_port_id',
        'transaction_currency', 'amount',
        'departure_date', 'estimated_arrival_date', 'actual_arrival_date',
        'status', 'current_latitude', 'current_longitude', 'notes',
    ];

    protected $casts = [
        'departure_date' => 'date',
        'estimated_arrival_date' => 'date',
        'actual_arrival_date' => 'date',
    ];

    public function originCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'origin_country_id');
    }

    public function destinationCountry(): BelongsTo
    {
        return $this->belongsTo(Country::class, 'destination_country_id');
    }

    public function originPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'origin_port_id');
    }

    public function destinationPort(): BelongsTo
    {
        return $this->belongsTo(Port::class, 'destination_port_id');
    }

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function latestRiskScore()
    {
        return $this->hasOne(RiskScore::class)->latestOfMany('calculated_at');
    }

    /**
     * Berapa hari lagi/sudah lewat dari estimasi tiba (ETA).
     * Positif = masih ada sisa waktu, negatif = sudah lewat ETA.
     */
    public function getDaysToEtaAttribute(): int
    {
        return now()->startOfDay()->diffInDays($this->estimated_arrival_date, false);
    }
}