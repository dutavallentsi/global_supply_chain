<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
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
     * Daftar pelabuhan untuk dropdown, dikelompokkan per negara.
     * GET /api/reference/ports
     */
    public function ports()
    {
        return Port::orderBy('name')->get(['id', 'name', 'country_id']);
    }
}
