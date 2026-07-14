<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'cca2', 'cca3', 'region', 'subregion', 'capital',
        'currency_code', 'currency_name', 'languages', 'flag_url',
        'latitude', 'longitude',
    ];

    protected $casts = [
        'languages' => 'array',
        'latitude' => 'decimal:6',
        'longitude' => 'decimal:6',
    ];

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function economicIndicators(): HasMany
    {
        return $this->hasMany(EconomicIndicator::class);
    }

    public function newsItems(): HasMany
    {
        return $this->hasMany(NewsItem::class);
    }

    public function shipmentsAsOrigin(): HasMany
    {
        return $this->hasMany(Shipment::class, 'origin_country_id');
    }

    public function shipmentsAsDestination(): HasMany
    {
        return $this->hasMany(Shipment::class, 'destination_country_id');
    }

    public function latestEconomicIndicator()
    {
        return $this->hasOne(EconomicIndicator::class)->latestOfMany('year');
    }
}