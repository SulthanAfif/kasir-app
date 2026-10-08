<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PosApiController;

Route::post('/login', [AuthApiController::class, 'login']);

Route::middleware('api.token')->group(function () {
    Route::get('/me', [AuthApiController::class, 'me']);
    Route::post('/logout', [AuthApiController::class, 'logout']);
    Route::get('/products', [PosApiController::class, 'products']);
    Route::post('/checkout', [PosApiController::class, 'checkout']);
    Route::get('/shifts/current', [PosApiController::class, 'shifts']);
});
