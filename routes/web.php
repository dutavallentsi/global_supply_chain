<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/shipments/{shipment}', [DashboardController::class, 'show'])->name('dashboard.show');

    Route::get('/country-comparison', [PageController::class, 'countryComparison'])->name('pages.country-comparison');
    Route::get('/port-location', [PageController::class, 'portLocation'])->name('pages.port-location');
    Route::get('/weather', [PageController::class, 'weather'])->name('pages.weather');
    Route::get('/currency', [PageController::class, 'currency'])->name('pages.currency');
    Route::get('/news', [PageController::class, 'news'])->name('pages.news');
    Route::get('/risk-analysis', [PageController::class, 'riskAnalysis'])->name('pages.risk-analysis');
});