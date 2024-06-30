<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:jwt'])->prefix('customer')->group(function () {
    Route::get('/', [OrderController::class, 'getCustomerOrder']);
    Route::get('history', [OrderController::class, 'getOrderHistory']);
    Route::get('ongoing', [OrderController::class, 'getOngoingOrder']);
    Route::get('available-vehicles', [VehicleController::class, 'getAvailableVehicle']);
    Route::get('{order_id}', [OrderController::class, 'getOrder']);

    Route::post('/', [OrderController::class, 'initiateOrder']);
    Route::post('{order_id}/place-order', [OrderController::class, 'placeOrder']);
    Route::post('{order_id}/add-rider-tip', [OrderController::class, 'addRiderTip']);
    Route::post('{order_id}/assign-rider', [OrderController::class, 'assignRider']);
    Route::post('{order_id}/rate-rider', [OrderController::class, 'rateRider']);
    Route::post('{order_id}/cancel-order', [OrderController::class, 'cancelOrder']);
});

Route::middleware(['auth:jwt'])->prefix('rider')->group(function () {
    Route::get('/', [OrderController::class, 'getRiderOrder']);
    Route::get('completed', [OrderController::class, 'getRiderCompletedOrder']);
    Route::get('current', [OrderController::class, 'getRiderCurrentOrder']);
    Route::get('failed', [OrderController::class, 'getRiderFailedOrder']);
    Route::get('new', [OrderController::class, 'getRiderNewOrder']);
    // Route::get('{order_id}', [OrderController::class, 'getOrder']);

    // Route::post('/', [OrderController::class, 'initiateOrder']);
    // Route::post('{order_id}/place-order', [OrderController::class, 'placeOrder']);
    // Route::post('{order_id}/add-rider-tip', [OrderController::class, 'addRiderTip']);
    // Route::post('{order_id}/assign-rider', [OrderController::class, 'assignRider']);
    // Route::post('{order_id}/rate-rider', [OrderController::class, 'rateRider']);
    // Route::post('{order_id}/cancel-order', [OrderController::class, 'cancelOrder']);
});
