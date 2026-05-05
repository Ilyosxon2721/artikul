<?php

declare(strict_types=1);

namespace App\Livewire\Onboarding;

use App\Enums\Currency;
use App\Enums\SellerLevel;
use App\Enums\UserMode;
use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Specialization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class SellerOnboarding extends Component
{
    public int $step = 1;

    /** @var array<int> */
    public array $specializationIds = [];

    /** @var array<int, string> map of marketplace_id => level */
    public array $marketplaceLevels = [];

    public ?float $hourlyRate = null;

    public string $currency = 'UZS';

    public ?int $hoursPerWeek = null;

    public function mount(): void
    {
        if (Auth::user()?->sellerProfile) {
            $this->redirect(route('dashboard'), navigate: true);
        }
    }

    public function next(): void
    {
        match ($this->step) {
            1 => $this->validate([
                'specializationIds' => ['required', 'array', 'min:1', 'max:5'],
                'specializationIds.*' => ['integer', 'exists:specializations,id'],
            ]),
            2 => $this->validate([
                'marketplaceLevels' => ['required', 'array', 'min:1'],
                'marketplaceLevels.*' => ['required', 'string', 'in:junior,middle,senior'],
            ]),
            default => null,
        };

        $this->step = min($this->step + 1, 3);
    }

    public function back(): void
    {
        $this->step = max($this->step - 1, 1);
    }

    public function finish(): void
    {
        $this->validate([
            'specializationIds' => ['required', 'array', 'min:1', 'max:5'],
            'marketplaceLevels' => ['required', 'array', 'min:1'],
            'hourlyRate' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'in:UZS,RUB,KZT,USD'],
            'hoursPerWeek' => ['nullable', 'integer', 'min:1', 'max:168'],
        ]);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        DB::transaction(function () use ($user): void {
            $profile = SellerProfile::firstOrCreate(['user_id' => $user->id]);
            $profile->fill([
                'hourly_rate' => $this->hourlyRate,
                'currency' => Currency::from($this->currency),
                'available_hours_per_week' => $this->hoursPerWeek,
            ])->save();

            $profile->specializations()->sync(array_fill_keys(
                $this->specializationIds,
                ['is_primary' => false],
            ));

            $pivot = [];
            foreach ($this->marketplaceLevels as $marketplaceId => $level) {
                $pivot[(int) $marketplaceId] = ['level' => $level, 'experience_months' => null];
            }
            $profile->marketplaces()->sync($pivot);

            $user->forceFill(['current_mode' => UserMode::Seller])->save();
        });

        session()->forget('mode_after_onboarding');

        $this->redirect(route('profile.seller.edit'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.onboarding.seller-onboarding', [
            'specializations' => Specialization::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name_ru')->get(),
            'marketplaces' => Marketplace::query()->where('is_active', true)->orderBy('sort_order')->get(),
            'levels' => SellerLevel::cases(),
        ]);
    }
}
