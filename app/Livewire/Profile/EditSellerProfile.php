<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\Currency;
use App\Models\Marketplace;
use App\Models\SellerProfile;
use App\Models\Specialization;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EditSellerProfile extends Component
{
    public string $headline = '';

    public string $bio = '';

    public string $videoIntroUrl = '';

    /** @var array<string> */
    public array $languages = [];

    public ?float $hourlyRate = null;

    public string $currency = 'UZS';

    public ?float $minOrderAmount = null;

    public ?int $hoursPerWeek = null;

    public bool $openToLongTerm = true;

    /** @var array<int> */
    public array $specializationIds = [];

    /** @var array<int, string> */
    public array $marketplaceLevels = [];

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $profile = SellerProfile::with(['specializations', 'marketplaces'])
            ->firstOrCreate(['user_id' => $user->id]);

        $this->headline = $profile->headline ?? '';
        $this->bio = $profile->bio ?? '';
        $this->videoIntroUrl = $profile->video_intro_url ?? '';
        $this->languages = (array) ($profile->languages ?? []);
        $this->hourlyRate = $profile->hourly_rate ? (float) $profile->hourly_rate : null;
        $this->currency = $profile->currency?->value ?? 'UZS';
        $this->minOrderAmount = $profile->min_order_amount ? (float) $profile->min_order_amount : null;
        $this->hoursPerWeek = $profile->available_hours_per_week;
        $this->openToLongTerm = (bool) $profile->open_to_long_term;
        $this->specializationIds = $profile->specializations->pluck('id')->all();
        $this->marketplaceLevels = $profile->marketplaces
            ->mapWithKeys(fn ($m) => [$m->id => (string) $m->pivot->level])
            ->all();
    }

    public function save(): void
    {
        $this->validate([
            'headline' => ['nullable', 'string', 'max:80'],
            'bio' => ['nullable', 'string', 'max:5000'],
            'videoIntroUrl' => ['nullable', 'url', 'max:255'],
            'languages' => ['array'],
            'languages.*' => ['string', 'max:32'],
            'hourlyRate' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required', 'in:UZS,RUB,KZT,USD'],
            'minOrderAmount' => ['nullable', 'numeric', 'min:0'],
            'hoursPerWeek' => ['nullable', 'integer', 'min:1', 'max:168'],
            'openToLongTerm' => ['boolean'],
            'specializationIds' => ['array', 'max:5'],
            'specializationIds.*' => ['integer', 'exists:specializations,id'],
            'marketplaceLevels' => ['array'],
            'marketplaceLevels.*' => ['nullable', 'in:junior,middle,senior'],
        ]);

        $user = Auth::user();
        abort_unless($user !== null, 403);

        DB::transaction(function () use ($user): void {
            $profile = SellerProfile::firstOrCreate(['user_id' => $user->id]);

            $profile->fill([
                'headline' => $this->headline ?: null,
                'bio' => $this->bio ?: null,
                'video_intro_url' => $this->videoIntroUrl ?: null,
                'languages' => $this->languages,
                'hourly_rate' => $this->hourlyRate,
                'currency' => Currency::from($this->currency),
                'min_order_amount' => $this->minOrderAmount,
                'available_hours_per_week' => $this->hoursPerWeek,
                'open_to_long_term' => $this->openToLongTerm,
            ])->save();

            $profile->specializations()->sync(array_fill_keys(
                $this->specializationIds,
                ['is_primary' => false],
            ));

            $pivot = [];
            foreach ($this->marketplaceLevels as $marketplaceId => $level) {
                if ($level === '' || $level === null) {
                    continue;
                }
                $pivot[(int) $marketplaceId] = ['level' => $level, 'experience_months' => null];
            }
            $profile->marketplaces()->sync($pivot);
        });

        session()->flash('status', __('app.profile.saved'));
    }

    public function render(): View
    {
        return view('livewire.profile.edit-seller-profile', [
            'specializations' => Specialization::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name_ru')->get(),
            'marketplaces' => Marketplace::query()->where('is_active', true)->orderBy('sort_order')->get(),
        ]);
    }
}
