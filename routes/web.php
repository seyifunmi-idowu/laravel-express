<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\BusinessController;

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


Route::get('/business/login', [BusinessController::class, 'showLoginForm'])->name('business-login');
Route::post('/business/login', [BusinessController::class, 'login']);
Route::get('/business/verify-email', [BusinessController::class, 'showVerifyEmailForm'])->name('business-verify-email');
Route::post('/business/verify-email', [BusinessController::class, 'verifyEmail']);
Route::get('/business/register', [BusinessController::class, 'showRegistrationForm'])->name('business-register');
Route::post('/business/register', [BusinessController::class, 'register']);
Route::get('/business/resend-otp', [BusinessController::class, 'resendOtp'])->name('business-resend-otp');
Route::get('/business/dashboard', [BusinessController::class, 'dashboard'])->name('business-dashboard');
Route::post('/lbusiness/ogout', [BusinessController::class, 'logout'])->name('business-logout')->middleware('auth');

Route::get('order', [BusinessController::class, 'order'])->name('business-order');
Route::get('order/{order_id}', [BusinessController::class, 'get_order'])->name('business-view-order');

Route::get('wallet', [BusinessController::class, 'index'])->name('business-wallet');
Route::post('fund-wallet', [BusinessController::class, 'fund'])->name('business-fund-wallet');

Route::post('paystack/callback', [BusinessController::class, 'verifyBusinessCardTransaction'])->name('business-verify-card-transaction');

Route::get('settings', [BusinessController::class, 'index'])->name('business-settings');
Route::delete('delete/card/{card_id}', [BusinessController::class, 'deleteCard'])->name('business-delete-card');

Route::post('regenerate-secret-key', [BusinessController::class, 'regenerateSecretKey'])->name('business-regenerate-secret-key');

// API DOCS
Route::get('api/docs', [BusinessController::class, 'docsIndex'])->name('business-api-docs');

