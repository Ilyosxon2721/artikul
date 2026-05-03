<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\BuyerProfile;
use App\Models\Marketplace;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EditBuyerProfile extends Component
{
    public string $companyName = '';

    public string $description = '';

    /** @var array<string> */
    public array $marketplaceCodes = [];

    public ?int $skusCount = null;

    public string $revenueRange = '';

    public string $defaultCurrency = 'UZS';

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $profile = BuyerProfile::firstOrCreate(['user_id' => $user->id]);

        $this->companyName = $profile->company_name ?? '';
        $this->description = $profile->description ?? '';
        $this->marketplaceCodes = (array) ($profile->marketplace_codes ?? []);
        $this->skusCount = $profile->skus_count;
        $this->revenueRange = $profile->revenue_range ?? '';
        $this->defaultCurrency = $profile->default_currency?->value ?? 'UZS';
    }

    public function save(): void
    {
        $this->validate([
            'companyName' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'marketplaceCodes' => ['array'],
            'marketplaceCodes.*' => ['string', 'exists:marketplaces,code'],
            'skusCount' => ['nullable', 'integer', 'min:0'],
            'revenueRange' => ['nullable', 'string', 'max:32'],
            'defaultCurrency' => ['required', 'in:UZS,RUB,KZT,USD'],
        ]);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        BuyerProfile::updateOrCreate(['user_id' => $user->id], [
            'company_name' => $this->companyName ?: null,
            'description' => $this->description ?: null,
            'marketplace_codes' => $this->marketplaceCodes,
            'skus_count' => $this->skusCount,
            'revenue_range' => $this->revenueRange ?: null,
            'default_currency' => $this->defaultCurrency,
        ]);

        session()->flash('status', __('app.profile.saved'));
    }

    public function render(): View
    {
        return view('livewire.profile.edit-buyer-profile', [
            'marketplaces' => Marketplace::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
