<?php

declare(strict_types=1);

use App\Enums\VerificationStatus;
use App\Filament\Admin\Resources\VerificationResource\Pages\ListVerifications;
use App\Models\SellerProfile;
use App\Models\User;
use App\Models\Verification;
use Filament\Facades\Filament;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->actingAs($this->admin);
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $this->seller = User::factory()->create();
    SellerProfile::create(['user_id' => $this->seller->id, 'is_verified' => false]);

    $this->verification = Verification::create([
        'user_id' => $this->seller->id,
        'status' => VerificationStatus::Pending,
        'id_document_path' => 'fake/id.pdf',
        'id_selfie_path' => 'fake/selfie.jpg',
        'marketplace_screenshots' => [],
    ]);
});

it('approve action flips status and verifies the seller', function () {
    Livewire::test(ListVerifications::class)
        ->callTableAction('approve', $this->verification);

    $this->verification->refresh();
    expect($this->verification->status)->toBe(VerificationStatus::Approved);
    expect($this->seller->fresh()->sellerProfile->is_verified)->toBeTrue();
});

it('reject action records the rejection reason', function () {
    Livewire::test(ListVerifications::class)
        ->callTableAction('reject', $this->verification, data: [
            'rejection_reason' => 'Низкое качество скриншота документа',
        ]);

    $this->verification->refresh();
    expect($this->verification->status)->toBe(VerificationStatus::Rejected);
    expect($this->verification->rejection_reason)->toContain('Низкое качество');
    expect($this->seller->fresh()->sellerProfile->is_verified)->toBeFalse();
});
