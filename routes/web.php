<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\HealthController;
use App\Http\Controllers\MarketplaceLandingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Telegram\WebhookController as TelegramWebhookController;
use App\Livewire\Chat\ChatPage;
use App\Livewire\Contracts\ContractShow;
use App\Livewire\Contracts\ContractsList;
use App\Livewire\Disputes\OpenDispute;
use App\Livewire\Landing\Home;
use App\Livewire\Onboarding\SellerOnboarding;
use App\Livewire\Profile\EditBuyerProfile;
use App\Livewire\Profile\EditGeneralProfile;
use App\Livewire\Profile\EditSellerProfile;
use App\Livewire\Profile\NotificationPreferences;
use App\Livewire\Profile\PortfolioManager;
use App\Livewire\Profile\ServicesManager;
use App\Livewire\Profile\TelegramLink;
use App\Livewire\Profile\TwoFactorSetup;
use App\Livewire\Proposals\MyProposals;
use App\Livewire\Proposals\TaskProposalsList;
use App\Livewire\Public\PublicBuyerProfile;
use App\Livewire\Public\PublicSellerProfile;
use App\Livewire\Referrals\ReferralDashboard;
use App\Livewire\Reviews\LeaveReview;
use App\Livewire\Search\GlobalSearch;
use App\Livewire\Search\MySavedSearches;
use App\Livewire\Sellers\SellerCatalog;
use App\Livewire\Tasks\TaskCatalog;
use App\Livewire\Tasks\TaskShow;
use App\Livewire\Tasks\TaskWizard;
use App\Livewire\Verification\SubmitVerification;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', Home::class)->name('home');

// Static pages
Route::view('/about', 'landing.about')->name('about');
Route::view('/how-it-works', 'landing.how-it-works')->name('how-it-works');
Route::view('/terms', 'landing.terms')->name('terms');
Route::view('/privacy', 'landing.privacy')->name('privacy');
Route::view('/for-sellers', 'landing.segments.sellers')->name('landing.sellers');
Route::view('/for-buyers', 'landing.segments.buyers')->name('landing.buyers');
Route::get('/marketplaces/{code}', MarketplaceLandingController::class)->name('landing.marketplace');

// Health, sitemap & Telegram webhook
Route::get('/health', HealthController::class)->name('health');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::post('/telegram/webhook', TelegramWebhookController::class)->name('telegram.webhook');

// Catalogs & search
Route::get('/tasks', TaskCatalog::class)->name('tasks.index');
Route::get('/sellers', SellerCatalog::class)->name('sellers.index');
Route::get('/search', GlobalSearch::class)->name('search');

// Authenticated task management — must come BEFORE /tasks/{slug} catch-all
Route::middleware('auth')->group(function (): void {
    Route::get('tasks/create', TaskWizard::class)->name('tasks.create');
    Route::get('tasks/{slug}/edit', TaskWizard::class)->name('tasks.edit');
});

// Task detail (after specific /tasks/* routes)
Route::get('/tasks/{slug}', TaskShow::class)->name('tasks.show');

// Public profiles (catalog at /sellers above wins for the exact match)
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

// Two-factor challenge (after primary login)
Volt::route('two-factor-challenge', 'pages.auth.two-factor-challenge')
    ->middleware('guest')
    ->name('two-factor.challenge');

// Password rotation page (admins forced to it after 180 days)
Volt::route('password/expired', 'pages.auth.password-expired')
    ->middleware('auth')
    ->name('password.expired');

Route::middleware('auth')->group(function (): void {
    Route::view('dashboard', 'dashboard')->middleware('verified')->name('dashboard');

    // Profile management
    Route::redirect('profile', 'profile/general')->name('profile');
    Route::get('profile/general', EditGeneralProfile::class)->name('profile.general');
    Route::get('profile/buyer', EditBuyerProfile::class)->name('profile.buyer.edit');
    Route::get('profile/seller', EditSellerProfile::class)->name('profile.seller.edit');
    Route::get('profile/notifications', TelegramLink::class)->name('profile.telegram');

    // Mode required prompt (placeholder)
    Route::get('mode/{mode}/required', function (string $mode) {
        return view('mode-required', ['mode' => $mode]);
    })->name('mode.required');

    // Seller onboarding wizard
    Route::get('onboarding/seller', SellerOnboarding::class)->name('onboarding.seller');

    // Proposals & contracts
    Route::get('dashboard/proposals', MyProposals::class)->name('proposals.mine');
    Route::get('tasks/{slug}/proposals', TaskProposalsList::class)->name('contracts.proposals.task');
    Route::get('contracts', ContractsList::class)->name('contracts.index');
    Route::get('contracts/{contract}', ContractShow::class)->name('contracts.show');

    // Chat
    Route::get('messages', ChatPage::class)->name('chat.index');
    Route::get('messages/{conversation}', ChatPage::class)->name('chat.show');

    // Reviews & disputes
    Route::get('contracts/{contract}/review', LeaveReview::class)->name('reviews.create');
    Route::get('contracts/{contract}/dispute', OpenDispute::class)->name('disputes.create');

    // Verification
    Route::get('profile/verification', SubmitVerification::class)->name('verification.show');

    // Referrals
    Route::get('dashboard/referrals', ReferralDashboard::class)->name('referrals.dashboard');

    // Portfolio, services, notification preferences
    Route::get('profile/portfolio', PortfolioManager::class)->name('profile.portfolio');
    Route::get('profile/services', ServicesManager::class)->name('profile.services');
    Route::get('profile/notification-preferences', NotificationPreferences::class)->name('profile.notification-prefs');
    Route::get('profile/two-factor', TwoFactorSetup::class)->name('profile.two-factor');

    // Saved searches
    Route::get('dashboard/saved-searches', MySavedSearches::class)->name('saved-searches.index');
});

require __DIR__.'/auth.php';
