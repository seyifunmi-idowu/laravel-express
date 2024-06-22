<?php

use App\Http\Controllers\Api\VehicleController;
use Illuminate\Support\Facades\Route;

Route::get('/available', [VehicleController::class, 'available_vehicle']);
