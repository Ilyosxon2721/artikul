<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ContractsController;
use App\Http\Controllers\Api\MeController;
use App\Http\Controllers\Api\ProposalsController;
use App\Http\Controllers\Api\ReviewsController;
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

    // Proposals
    Route::get('proposals', [ProposalsController::class, 'index'])->name('api.proposals.index');
    Route::post('tasks/{task:slug}/proposals', [ProposalsController::class, 'store'])->name('api.proposals.store');
    Route::delete('proposals/{proposal}', [ProposalsController::class, 'destroy'])->name('api.proposals.destroy');
    Route::post('proposals/{proposal}/accept', [ProposalsController::class, 'accept'])->name('api.proposals.accept');

    // Contracts
    Route::get('contracts', [ContractsController::class, 'index'])->name('api.contracts.index');
    Route::get('contracts/{contract}', [ContractsController::class, 'show'])->name('api.contracts.show');
    Route::post('contracts/{contract}/submit', [ContractsController::class, 'submit'])->name('api.contracts.submit');
    Route::post('contracts/{contract}/approve', [ContractsController::class, 'approve'])->name('api.contracts.approve');
    Route::post('contracts/{contract}/revision', [ContractsController::class, 'revision'])->name('api.contracts.revision');
    Route::post('contracts/{contract}/dispute', [ContractsController::class, 'dispute'])->name('api.contracts.dispute');

    // Reviews
    Route::post('contracts/{contract}/reviews', [ReviewsController::class, 'store'])->name('api.reviews.store');
});
