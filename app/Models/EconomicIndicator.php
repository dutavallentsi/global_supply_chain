<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EconomicIndicator extends Model
{
    protected $fillable = [
        'country_id', 'year', 'gdp_usd', 'inflation_rate',
        'population', 'exports_value_usd', 'imports_value_usd',
    ];

    protected $casts = [
        'gdp_usd' => 'float',
        'inflation_rate' => 'float',
        'population' => 'integer',
        'exports_value_usd' => 'float',
        'imports_value_usd' => 'float',
        'year' => 'integer',
    ];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}