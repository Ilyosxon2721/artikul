<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\GoogleController;
use App\Livewire\Onboarding\SellerOnboarding;
use App\Livewire\Profile\EditBuyerProfile;
use App\Livewire\Profile\EditGeneralProfile;
use App\Livewire\Profile\EditSellerProfile;
use App\Livewire\Public\PublicBuyerProfile;
use App\Livewire\Public\PublicSellerProfile;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'welcome')->name('home');

// Public profiles
Route::get('/buyers/{username}', PublicBuyerProfile::class)->name('public.buyer');
Route::get('/sellers/{username}', PublicSellerProfile::class)->name('public.seller');

// Locale switch
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, config('app.available_locales', ['ru', 'uz', 'en']), true)) {
        session()->put('locale', $locale);
        if (auth()->check()) {
            auth()->user()->forceFill(['locale' => $locale])->save();
        }
    }

    return back();
})->name('locale.switch');

// Google OAuth
Route::get('/auth/google/redirect', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback'])->name('auth.google.callback');

// Phone verification (used during registration)
Volt::route('register/verify-phone', 'pages.auth.verify-phone')
    ->middleware('guest')
    ->name('register.verify-phone');

Route::middleware('auth')->group(function (): void {
    Route::view('dashboard', 'dashboard')->middleware('verified')->name('dashboard');

    // Profile management
    Route::redirect('profile', 'profile/general')->name('profile');
    Route::get('profile/general', EditGeneralProfile::class)->name('profile.general');
    Route::get('profile/buyer', EditBuyerProfile::class)->name('profile.buyer.edit');
    Route::get('profile/seller', EditSellerProfile::class)->name('profile.seller.edit');

    // Mode required prompt (placeholder)
    Route::get('mode/{mode}/required', function (string $mode) {
        return view('mode-required', ['mode' => $mode]);
    })->name('mode.required');

    // Seller onboarding wizard
    Route::get('onboarding/seller', SellerOnboarding::class)->name('onboarding.seller');
});

require __DIR__.'/auth.php';
