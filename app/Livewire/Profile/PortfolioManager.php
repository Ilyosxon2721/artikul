<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Models\Portfolio;
use App\Models\SellerProfile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class PortfolioManager extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public $cover = null;

    public string $clientName = '';

    public ?string $completedOn = null;

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
            'title' => ['required', 'string', 'min:3', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'clientName' => ['nullable', 'string', 'max:120'],
            'completedOn' => ['nullable', 'date'],
        ]);

        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();

        $coverUrl = null;
        if ($this->cover) {
            $coverUrl = '/storage/'.$this->cover->store('portfolio', 'public');
        }

        $payload = [
            'title' => $this->title,
            'description' => $this->description ?: null,
            'client_name' => $this->clientName ?: null,
            'completed_on' => $this->completedOn ?: null,
            'is_visible' => true,
        ];

        if ($coverUrl !== null) {
            $payload['cover_url'] = $coverUrl;
        }

        if ($this->editingId !== null) {
            $portfolio = Portfolio::query()->where('id', $this->editingId)->where('seller_profile_id', $profile->id)->firstOrFail();
            $portfolio->fill($payload)->save();
        } else {
            Portfolio::create(array_merge($payload, ['seller_profile_id' => $profile->id]));
        }

        $this->resetForm();
        session()->flash('status', __('app.portfolio.saved'));
    }

    public function edit(int $id): void
    {
        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();
        $item = Portfolio::query()->where('id', $id)->where('seller_profile_id', $profile->id)->firstOrFail();

        $this->editingId = $item->id;
        $this->title = $item->title;
        $this->description = $item->description ?? '';
        $this->clientName = $item->client_name ?? '';
        $this->completedOn = $item->completed_on?->format('Y-m-d');
        $this->cover = null;
    }

    public function delete(int $id): void
    {
        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();
        Portfolio::query()->where('id', $id)->where('seller_profile_id', $profile->id)->delete();
    }

    public function resetForm(): void
    {
        $this->reset(['title', 'description', 'cover', 'clientName', 'completedOn', 'editingId']);
    }

    public function render(): View
    {
        $profile = SellerProfile::where('user_id', Auth::id())->firstOrFail();

        return view('livewire.profile.portfolio-manager', [
            'items' => Portfolio::query()->where('seller_profile_id', $profile->id)->orderBy('sort_order')->latest()->get(),
        ]);
    }
}
