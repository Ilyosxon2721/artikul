<?php

declare(strict_types=1);

use App\Enums\UserMode;
use App\Livewire\Onboarding\SellerOnboarding;
use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Specialization;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create(['current_mode' => UserMode::Buyer]);
    $this->actingAs($this->user);

    $this->marketplace = Marketplace::query()->firstOrCreate(['code' => 'WB'], [
        'name_ru' => 'Wildberries', 'name_uz' => 'Wildberries', 'name_en' => 'Wildberries',
        'country_code' => 'RU', 'is_active' => true, 'sort_order' => 1,
    ]);

    $this->specialization = Specialization::query()->firstOrCreate(['slug' => 'wb-mgr'], [
        'name_ru' => 'Менеджер WB', 'name_uz' => 'WB menejeri', 'name_en' => 'WB manager',
        'is_active' => true, 'sort_order' => 1,
    ]);
});

it('walks through all three steps and saves the seller profile', function () {
    Livewire::test(SellerOnboarding::class)
        ->set('specializationIds', [$this->specialization->id])
        ->call('next')
        ->assertHasNoErrors()
        ->set('marketplaceLevels.'.$this->marketplace->id, 'middle')
        ->call('next')
        ->assertHasNoErrors()
        ->set('hourlyRate', 25)
        ->set('currency', 'USD')
        ->set('hoursPerWeek', 40)
        ->call('finish')
        ->assertHasNoErrors()
        ->assertRedirect();

    $profile = SellerProfile::where('user_id', $this->user->id)->first();

    expect($profile)->not->toBeNull();
    expect((float) $profile->hourly_rate)->toBe(25.0);
    expect($profile->specializations->pluck('id')->all())->toEqual([$this->specialization->id]);
    expect($profile->marketplaces->first()->pivot->level)->toBe('middle');
    expect($this->user->fresh()->current_mode)->toBe(UserMode::Seller);
});

it('rejects submitting with no specializations on step 1', function () {
    Livewire::test(SellerOnboarding::class)
        ->set('specializationIds', [])
        ->call('next')
        ->assertHasErrors('specializationIds');
});
