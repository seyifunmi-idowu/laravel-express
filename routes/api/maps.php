<?php

use App\Http\Controllers\Api\CustomerController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::post('address-info', [CustomerController::class, 'addressInfo']);
});
