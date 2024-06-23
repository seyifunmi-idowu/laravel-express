<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::get('/', [WalletController::class, 'getUserCards']);
    Route::post('initiate', [WalletController::class, 'initiateCardTransaction']);
    Route::post('debit', [WalletController::class, 'debitCard']);
});
