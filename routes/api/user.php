<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::get('me', [UserController::class, 'getUserInfo']);
    Route::delete('delete', [UserController::class, 'deleteUser']);
    Route::post('logout', [UserController::class, 'logoutUser']);
    Route::post('change/password', [UserController::class, 'changePassword']);
    Route::post('customize-referral-code', [UserController::class, 'customizeReferralCode']);
});
