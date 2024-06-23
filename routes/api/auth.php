<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;


Route::prefix('password-reset')->group(function () {
    Route::post('initiate', [UserController::class, 'initiatePasswordReset']);
    Route::post('verify', [UserController::class, 'verifyPasswordReset']);
});
