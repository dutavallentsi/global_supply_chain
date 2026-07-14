<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi ke countries.dev (https://countries.dev).
 *
 * CATATAN MIGRASI: Awalnya service ini memakai RestCountries API v3.1
 * (restcountries.com/v3.1), tapi API tersebut sudah DEPRECATED oleh
 * penyedianya per pertengahan 2026. Versi penggantinya (v5) mewajibkan
 * API key dengan kuota gratis 500 request/bulan.
 *
 * countries.dev dipilih sebagai pengganti karena gratis, tanpa API key,
 * dan strukturnya sengaja dibuat mirip API lama (flat, bukan nested).
 * Referensi: https://countries.dev/blog/migrate-from-restcountries
 */
class RestCountriesService
{
    protected string $baseUrl = 'https://countries.dev';

    /**
     * Ambil detail satu negara berdasarkan kode ISO alpha-2 (misal: ID, US, JP).
     */
    public function getByCode(string $cca2): ?array
    {
        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/alpha/{$cca2}");

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();
            if (! is_array($data) || empty($data)) {
                return null;
            }

            return $this->normalize($data);
        } catch (\Throwable $e) {
            Log::error('RestCountriesService error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Ambil seluruh negara sekaligus (dipakai saat seeding awal database).
     */
    public function getAll(): array
    {
        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/countries", [
                'limit' => 300, // pastikan tidak kepotong pagination (dunia ada ~250 negara)
            ]);

            if (! $response->successful()) {
                Log::warning('countries.dev getAll failed', ['status' => $response->status(), 'body' => $response->body()]);
                return [];
            }

            $data = $response->json();

            if (! is_array($data)) {
                Log::warning('countries.dev getAll: unexpected response shape', ['body' => $response->body()]);
                return [];
            }

            // Buang elemen yang bukan array (data negara tidak valid/kosong)
            $validCountries = array_filter($data, fn ($c) => is_array($c));

            return array_map(fn ($c) => $this->normalize($c), $validCountries);
        } catch (\Throwable $e) {
            Log::error('RestCountriesService getAll error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Normalisasi response flat dari countries.dev ke struktur tabel countries kita.
     *
     * Contoh response asli (GET /alpha/FR):
     * {
     *   "name": "France", "alpha2Code": "FR", "alpha3Code": "FRA",
     *   "region": "Europe", "subregion": "Western Europe", "capital": "Paris",
     *   "currencies": [{"code":"EUR","name":"Euro","symbol":"€"}],
     *   "languages": [{"name":"French","iso639_1":"fr", ...}],
     *   "flags": {"png": "...", "svg": "..."},
     *   "latlng": [46, 2]
     * }
     */
    protected function normalize(array $c): array
    {
        $currencyCode = $c['currencies'][0]['code'] ?? null;
        $currencyName = $c['currencies'][0]['name'] ?? null;

        $languages = array_map(
            fn ($lang) => $lang['name'] ?? null,
            $c['languages'] ?? []
        );

        return [
            'name' => $c['name'] ?? 'Unknown',
            'cca2' => $c['alpha2Code'] ?? null,
            'cca3' => $c['alpha3Code'] ?? null,
            'region' => $c['region'] ?? null,
            'subregion' => $c['subregion'] ?? null,
            'capital' => $c['capital'] ?? null, // sudah string, bukan array
            'currency_code' => $currencyCode,
            'currency_name' => $currencyName,
            'languages' => array_values(array_filter($languages)),
            'flag_url' => $c['flags']['svg'] ?? ($c['flags']['png'] ?? null),
            'latitude' => $c['latlng'][0] ?? null,
            'longitude' => $c['latlng'][1] ?? null,
        ];
    }
}
