<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\Currency;
use App\Models\SellerProfile;
use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ServicesManager extends Component
{
    public string $title = '';

    public string $description = '';

    public ?float $price = null;

    public string $currency = 'UZS';

    public ?int $deliveryDays = null;

    public ?int $editingId = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);
        SellerProfile::firstOrCreate(['user_id' => $user->id]);
    }

    public function save(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'min:5', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'in:UZS,RUB,KZT,USD'],
            'deliveryDays' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();

        $payload = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'price' => $this->price,
            'currency' => Currency::from($this->currency),
            'delivery_days' => $this->deliveryDays,
            'is_active' => true,
        ];

        if ($this->editingId !== null) {
            $service = Service::query()->where('id', $this->editingId)->where('seller_profile_id', $profile->id)->firstOrFail();
            $service->fill($payload)->save();
        } else {
            Service::create(array_merge($payload, ['seller_profile_id' => $profile->id]));
        }

        $this->resetForm();
        session()->flash('status', __('app.services.saved'));
    }

    public function edit(int $id): void
    {
        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();
        $service = Service::query()->where('id', $id)->where('seller_profile_id', $profile->id)->firstOrFail();

        $this->editingId = $service->id;
        $this->title = $service->title;
        $this->description = $service->description ?? '';
        $this->price = $service->price !== null ? (float) $service->price : null;
        $this->currency = $service->currency?->value ?? 'UZS';
        $this->deliveryDays = $service->delivery_days;
    }

    public function delete(int $id): void
    {
        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();
        Service::query()->where('id', $id)->where('seller_profile_id', $profile->id)->delete();
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'description', 'price', 'currency', 'deliveryDays', 'editingId']);
        $this->currency = 'UZS';
    }

    public function render(): View
    {
        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();

        return view('livewire.profile.services-manager', [
            'services' => Service::query()->where('seller_profile_id', $profile->id)->latest()->get(),
        ]);
    }
}
