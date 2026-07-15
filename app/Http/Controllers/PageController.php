<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Models\Shipment;

class PageController extends Controller
{
    public function countryComparison()
    {
        $countries = Country::whereHas('economicIndicators')->orderBy('name')->get(['id', 'name', 'cca2', 'flag_url']);
        return view('pages.country-comparison', compact('countries'));
    }

    public function portLocation()
    {
        return view('pages.port-location');
    }

    public function weather()
    {
        $ports = Port::with('country:id,name')->orderBy('name')->get(['id', 'name', 'country_id', 'latitude', 'longitude']);
        return view('pages.weather', compact('ports'));
    }

    public function currency()
    {
        return view('pages.currency');
    }

    public function news()
    {
        $countries = Country::orderBy('name')->get(['id', 'name', 'cca2']);
        return view('pages.news', compact('countries'));
    }

    public function riskAnalysis()
    {
        $shipments = Shipment::with(['originCountry', 'destinationCountry', 'latestRiskScore'])
            ->get()
            ->sortByDesc(fn ($s) => $s->latestRiskScore->total_risk_score ?? -1)
            ->values();

        return view('pages.risk-analysis', compact('shipments'));
    }
}