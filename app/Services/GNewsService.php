<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integrasi ke GNews API (https://gnews.io).
 * Simpan API key di .env: GNEWS_API_KEY
 */
class GNewsService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://gnews.io/api/v4/search';

    protected array $keywordsByCategory = [
        'geopolitical' => ['conflict', 'sanction', 'war', 'tension', 'embargo'],
        'economic' => ['inflation', 'recession', 'interest rate', 'economic crisis'],
        'logistics' => ['port congestion', 'shipping delay', 'strike port', 'supply chain disruption'],
    ];

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key');
    }

    /**
     * Cari berita terkait satu negara untuk kategori tertentu.
     */
    public function searchNews(string $countryName, string $category = 'logistics', int $max = 5): array
    {
        $keywords = $this->keywordsByCategory[$category] ?? [$category];
        $query = $countryName . ' ' . implode(' OR ', $keywords);

        try {
            $response = Http::timeout(10)->get($this->baseUrl, [
                'q' => $query,
                'lang' => 'en',
                'max' => $max,
                'apikey' => $this->apiKey,
            ]);

            if (! $response->successful()) {
                Log::warning('GNews API failed', ['status' => $response->status()]);
                return [];
            }

            return collect($response->json('articles', []))->map(fn ($a) => [
                'title' => $a['title'],
                'url' => $a['url'],
                'source' => $a['source']['name'] ?? null,
                'published_at' => $a['publishedAt'],
                'category' => $category,
            ])->all();
        } catch (\Throwable $e) {
            Log::error('GNewsService error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Hitung skor risiko geopolitik sederhana berdasarkan jumlah berita
     * negatif (geopolitical + logistics) dalam periode tertentu.
     * Skala 0-100.
     */
    public function estimateGeopoliticalRiskScore(string $countryName): float
    {
        $geo = count($this->searchNews($countryName, 'geopolitical', 10));
        $logistics = count($this->searchNews($countryName, 'logistics', 10));

        $totalHits = $geo + $logistics;

        // Sederhana: makin banyak berita negatif ditemukan, makin tinggi skor.
        // 0 berita = 10 (baseline), 10+ berita = 100 (capped).
        return (float) min(100, 10 + ($totalHits * 9));
    }
}