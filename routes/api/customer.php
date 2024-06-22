<?php

use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->group(function () {
    //Authentication
    Route::post('register', [CustomerController::class, 'registerCustomer']);
    Route::post('verify', [CustomerController::class, 'verifyOtp']);
    Route::post('login', [CustomerController::class, 'login']);
    Route::post('register/resend', [CustomerController::class, 'resendOtp']);
    Route::post('register/complete', [CustomerController::class, 'completeSignup']);
    Route::post('register/change/phone-number', [CustomerController::class, 'changePhoneNumer']);
    Route::post('register/change/email', [CustomerController::class, 'changeEmail']);
});

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::get('info', [CustomerController::class, 'customerInfo']);
    Route::get('favourite-rider', [CustomerController::class, 'getCustomerFavouriteRider']);
    Route::post('register/complete', [CustomerController::class, 'completeBusinessCustomerSignup']);
    Route::post('update-profile', [CustomerController::class, 'updateProfile']);
});

Route::middleware(['auth:api'])->group(function () {
    Route::post('saved-address', [CustomerController::class, 'createAddress']);
    Route::get('saved-address', [CustomerController::class, 'getAddress']);
    Route::delete('saved-address/{id}', [CustomerController::class, 'destroyAddress']);
    Route::patch('saved-address/{id}', [CustomerController::class, 'updateAddress']);
});
