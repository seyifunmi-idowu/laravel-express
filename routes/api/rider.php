<?php

use App\Http\Controllers\Api\RiderController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    //Authentication
    Route::get('available-cities', [RiderController::class, 'availableCities']);
    Route::post('register', [RiderController::class, 'registerRider']);
    Route::post('verify', [RiderController::class, 'verifyOtp']);
    Route::post('login', [RiderController::class, 'login']);
    Route::post('register/resend', [RiderController::class, 'resendOtp']);
    Route::post('register/change/phone-number', [RiderController::class, 'changePhoneNumer']);
    Route::post('register/change/email', [RiderController::class, 'changeEmail']);
});

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::get('info', [RiderController::class, 'riderInfo']);
    Route::get('home', [RiderController::class, 'getRiderHome']);
    Route::get('performance', [RiderController::class, 'getRiderPerformance']);
    Route::get('vehicle/info', [RiderController::class, 'getRiderVehicle']);
    Route::patch('vehicle/info', [RiderController::class, 'updateRiderVehicle']);
    Route::post('on-duty', [RiderController::class, 'setRiderDuty']);
});

Route::middleware(['auth:jwt'])->prefix('kyc')->group(function () {
    Route::post('submit', [RiderController::class, 'submitKyc']);
    Route::post('upload', [RiderController::class, 'uploadDocument']);
    Route::get('info', [RiderController::class, 'getKycInfo']);
});
