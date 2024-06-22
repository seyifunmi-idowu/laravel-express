<?php

use App\Http\Controllers\Api\NotificationController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::get('/', [NotificationController::class, 'getNotifications']);
    Route::post('{id}/opened', [NotificationController::class, 'openedNotifications']);
});
