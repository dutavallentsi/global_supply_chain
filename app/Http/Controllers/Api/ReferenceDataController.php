<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\ExchangeRate;
use App\Models\Port;

class ReferenceDataController extends Controller
{
    /**
     * Daftar negara untuk dropdown asal/tujuan shipment.
     * GET /api/reference/countries
     */
    public function countries()
    {
        return Country::orderBy('name')->get(['id', 'name', 'cca2', 'currency_code']);
    }

    /**
     * Daftar pelabuhan untuk dropdown, termasuk nama negaranya.
     * GET /api/reference/ports
     */
    public function ports()
    {
        return Port::with('country:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'country_id', 'latitude', 'longitude'])
            ->map(fn ($port) => [
                'id' => $port->id,
                'name' => $port->name,
                'country_id' => $port->country_id,
                'country_name' => $port->country->name,
            ]);
    }

    /**
     * Daftar kode mata uang yang tersedia datanya (untuk dropdown halaman Currency).
     * GET /api/reference/currencies
     */
    public function currencies()
    {
        return ExchangeRate::where('base_currency', 'USD')
            ->distinct()
            ->orderBy('target_currency')
            ->pluck('target_currency');
    }
}