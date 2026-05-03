<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MeController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function (): void {
    Route::post('register', [AuthController::class, 'register'])->middleware('throttle:register')->name('api.auth.register');
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:login')->name('api.auth.login');
    Route::post('phone/send-code', [AuthController::class, 'sendPhoneCode'])->middleware('throttle:phone-code')->name('api.auth.phone.send-code');
    Route::post('phone/verify', [AuthController::class, 'verifyPhone'])->middleware('throttle:phone-code')->name('api.auth.phone.verify');
});

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
    Route::post('auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');

    Route::get('me', [MeController::class, 'show'])->name('api.me.show');
    Route::put('me', [MeController::class, 'update'])->name('api.me.update');
    Route::put('me/buyer', [MeController::class, 'updateBuyer'])->name('api.me.buyer.update');
    Route::put('me/seller', [MeController::class, 'updateSeller'])->name('api.me.seller.update');
    Route::post('me/avatar', [MeController::class, 'uploadAvatar'])->name('api.me.avatar');
    Route::post('me/mode', [MeController::class, 'switchMode'])->name('api.me.mode');
});
