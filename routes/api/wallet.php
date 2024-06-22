<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::get('balance', [WalletController::class, 'getWalletalance']);
    Route::get('transaction', [WalletController::class, 'getUserTransaction']);
    Route::post('transfer/bank', [WalletController::class, 'transferToBank']);
    Route::post('transfer/beneficiary', [WalletController::class, 'transferToBeneficiary']);
});
