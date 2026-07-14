<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    protected $fillable = [
        'base_currency', 'target_currency', 'rate', 'rate_date',
    ];

    protected $casts = [
        'rate_date' => 'date',
        'rate' => 'decimal:6',
    ];
}