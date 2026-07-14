<?php

namespace App\Services;

use App\Models\RiskScore;
use App\Models\Shipment;
use App\Models\WeatherSnapshot;
use Carbon\Carbon;

/**
 * Menggabungkan semua sinyal risiko (cuaca, kemacetan pelabuhan,
 * geopolitik, kurs, inflasi) menjadi satu skor risiko per shipment.
 *
 * Bobot masing-masing komponen bisa disesuaikan di properti $weights.
 */
class RiskCalculatorService
{
    protected array $weights = [
        'weather' => 0.25,
        'port_congestion' => 0.20,
        'geopolitical' => 0.20,
        'currency' => 0.20,
        'inflation' => 0.15,
    ];

    public function __construct(
        protected OpenMeteoService $weather,
        protected ExchangeRateService $exchangeRate,
        protected GNewsService $news,
    ) {}

    public function calculateForShipment(Shipment $shipment): RiskScore
    {
        $weatherRisk = $this->calculateWeatherRisk($shipment);
        $portCongestionRisk = $this->calculatePortCongestionRisk($shipment);
        $geopoliticalRisk = $this->calculateGeopoliticalRisk($shipment);
        $currencyRisk = $this->calculateCurrencyRisk($shipment);
        $inflationRisk = $this->calculateInflationRisk($shipment);

        $total =
            $weatherRisk * $this->weights['weather'] +
            $portCongestionRisk * $this->weights['port_congestion'] +
            $geopoliticalRisk * $this->weights['geopolitical'] +
            $currencyRisk * $this->weights['currency'] +
            $inflationRisk * $this->weights['inflation'];

        return RiskScore::create([
            'shipment_id' => $shipment->id,
            'weather_risk' => round($weatherRisk, 2),
            'port_congestion_risk' => round($portCongestionRisk, 2),
            'geopolitical_risk' => round($geopoliticalRisk, 2),
            'currency_risk' => round($currencyRisk, 2),
            'inflation_risk' => round($inflationRisk, 2),
            'total_risk_score' => round($total, 2),
            'risk_level' => $this->classifyLevel($total),
            'calculated_at' => Carbon::now(),
        ]);
    }

    protected function calculateWeatherRisk(Shipment $shipment): float
    {
        $map = ['low' => 15, 'medium' => 45, 'high' => 75, 'severe' => 100];

        $destPort = $shipment->destinationPort;
        if (! $destPort) {
            return 0;
        }

        $data = $this->weather->getCurrentWeather((float) $destPort->latitude, (float) $destPort->longitude);
        if (! $data) {
            return 0;
        }

        WeatherSnapshot::create([
            'port_id' => $destPort->id,
            'latitude' => $destPort->latitude,
            'longitude' => $destPort->longitude,
            'temperature_c' => $data['temperature_c'],
            'precipitation_mm' => $data['precipitation_mm'],
            'wind_speed_kmh' => $data['wind_speed_kmh'],
            'storm_risk_level' => $data['storm_risk_level'],
            'recorded_at' => now(),
        ]);

        return (float) ($map[$data['storm_risk_level']] ?? 15);
    }

    /**
     * NOTE: Data kemacetan pelabuhan real-time (seperti MarineTraffic)
     * umumnya API berbayar. Sebagai proxy gratis, skor ini diturunkan
     * dari jumlah berita "port congestion / strike port" terbaru
     * terkait negara tujuan (lihat GNewsService kategori 'logistics').
     */
    protected function calculatePortCongestionRisk(Shipment $shipment): float
    {
        $countryName = $shipment->destinationCountry->name;
        $hits = count($this->news->searchNews($countryName, 'logistics', 10));

        return (float) min(100, 10 + ($hits * 10));
    }

    protected function calculateGeopoliticalRisk(Shipment $shipment): float
    {
        return $this->news->estimateGeopoliticalRiskScore($shipment->destinationCountry->name);
    }

    protected function calculateCurrencyRisk(Shipment $shipment): float
    {
        $base = $shipment->transaction_currency;
        $target = $shipment->destinationCountry->currency_code ?? 'USD';

        if ($base === $target) {
            return 0;
        }

        $volatility = $this->exchangeRate->calculateVolatility($base, $target, 30);

        // Volatilitas (coefficient of variation %) dipetakan ke skala 0-100.
        // >5% CV dalam 30 hari sudah tergolong sangat tinggi untuk pasangan mata uang mayor.
        return (float) min(100, $volatility * 20);
    }

    protected function calculateInflationRisk(Shipment $shipment): float
    {
        $indicator = $shipment->originCountry->latestEconomicIndicator;
        if (! $indicator || $indicator->inflation_rate === null) {
            return 0;
        }

        $inflation = (float) $indicator->inflation_rate;

        // Skala kasar: 0% = 0 risiko, 20%+ inflasi = risiko maksimum
        return (float) min(100, max(0, ($inflation / 20) * 100));
    }

    protected function classifyLevel(float $score): string
    {
        return match (true) {
            $score >= 75 => 'critical',
            $score >= 50 => 'high',
            $score >= 25 => 'medium',
            default => 'low',
        };
    }
}