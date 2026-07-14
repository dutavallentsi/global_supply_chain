<?php

use App\Http\Controllers\Api\ChartDataController;
use App\Http\Controllers\Api\MapDataController;
use App\Http\Controllers\Api\ReferenceDataController;
use App\Http\Controllers\Api\ShipmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('reference')->group(function () {
    Route::get('/countries', [ReferenceDataController::class, 'countries']);
    Route::get('/ports', [ReferenceDataController::class, 'ports']);
});

Route::prefix('shipments')->group(function () {
    Route::get('/', [ShipmentController::class, 'index']);
    Route::post('/', [ShipmentController::class, 'store']);
    Route::patch('/{shipment}', [ShipmentController::class, 'update']);
    Route::delete('/{shipment}', [ShipmentController::class, 'destroy']);
    Route::post('/{shipment}/recalculate-risk', [ShipmentController::class, 'recalculateRisk']);
});

Route::prefix('charts')->group(function () {
    Route::get('/exchange-rate', [ChartDataController::class, 'exchangeRateHistory']);
    Route::get('/economic/{country}', [ChartDataController::class, 'economicIndicators']);
});

Route::prefix('map')->group(function () {
    Route::get('/ports', [MapDataController::class, 'ports']);
    Route::get('/shipments', [MapDataController::class, 'shipments']);
});
