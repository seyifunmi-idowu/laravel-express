<?php

use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::get('paystack/callback', [WalletController::class, 'paystackCallback']);
Route::post('paystack/webhook', [WalletController::class, 'paystackWebhook']);
