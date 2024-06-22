<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['auth:jwt']], function () {
    Route::get('list', [WalletController::class, 'getListOfBank']);
    Route::get('user-beneficiary', [WalletController::class, 'getUserBeneficiary']);
    Route::post('account-number', [WalletController::class, 'verifyAccountNumber']);
});
