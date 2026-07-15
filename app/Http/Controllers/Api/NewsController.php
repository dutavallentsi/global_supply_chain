<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\GNewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(protected GNewsService $newsService) {}

    /**
     * Cari berita terkait satu negara berdasarkan kategori.
     * GET /api/news?country_id=&category=
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'country_id' => 'required|exists:countries,id',
            'category' => 'sometimes|in:geopolitical,economic,logistics',
        ]);

        $country = Country::findOrFail($validated['country_id']);
        $category = $validated['category'] ?? 'logistics';

        $articles = $this->newsService->searchNews($country->name, $category, 10);

        return response()->json([
            'country' => $country->name,
            'category' => $category,
            'articles' => $articles,
        ]);
    }
}