<?php

use Illuminate\Support\Facades\Route;
use App\Helpers\ApiResponse;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('login', function () {
    return ApiResponse::responseUnauthorized();
})->name('login');

Route::fallback(function () {
    return ApiResponse::responseError(
        [
            'Device Info' => request()->header('User-Agent') ?? '',
            'Your IP' => request()->ip() ?? ''
        ],
        'Page Not Found. If error persists, contact feleexpress@gmail.com',
        404
    );
});
