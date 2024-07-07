<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BusinessController;
use App\Livewire\Auth\CustomLogin;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin/login', CustomLogin::class)->name('filament.admin.auth.login');


Route::get('/business/login', [BusinessController::class, 'showLoginForm'])->name('business-login');
Route::post('/business/login', [BusinessController::class, 'login']);
Route::get('/business/verify-email', [BusinessController::class, 'showVerifyEmailForm'])->name('business-verify-email');
Route::post('/business/verify-email', [BusinessController::class, 'verifyEmail']);
Route::get('/business/register', [BusinessController::class, 'showRegistrationForm'])->name('business-register');
Route::post('/business/register', [BusinessController::class, 'register']);
Route::get('/business/resend-otp', [BusinessController::class, 'resendOtp'])->name('business-resend-otp');
Route::get('/business/logout', [BusinessController::class, 'logout'])->name('business-logout')->middleware('web');

Route::get('/business/dashboard', [BusinessController::class, 'dashboard'])->name('business-dashboard')->middleware('web');
Route::get('business/order', [BusinessController::class, 'order'])->name('business-order');
Route::get('business/order/{order_id}', [BusinessController::class, 'get_order'])->name('business-view-order');

Route::get('business/wallet', [BusinessController::class, 'wallet'])->name('business-wallet');
Route::post('business/fund-wallet', [BusinessController::class, 'fundWallet'])->name('business-fund-wallet');
Route::get('business/card/{card_id}/delete', [BusinessController::class, 'deleteCard'])->name('business-delete-card');

Route::get('business/paystack/callback', [BusinessController::class, 'verifyBusinessCardTransaction'])->name('business-verify-card-transaction');

Route::get('business/settings', [BusinessController::class, 'settings'])->name('business-settings');
Route::post('business/update-webhook', [BusinessController::class, 'updateWebhook'])->name('business-update-webhook');

Route::get('business/regenerate-secret-key', [BusinessController::class, 'regenerateSecretKey'])->name('business-regenerate-secret-key');

// API DOCS
Route::get('business/api/docs', [BusinessController::class, 'docsIndex'])->name('business-api-docs');

