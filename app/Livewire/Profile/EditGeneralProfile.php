<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class EditGeneralProfile extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $username = '';

    public string $email = '';

    public string $phone = '';

    public string $countryCode = '';

    public string $city = '';

    public string $timezone = 'Asia/Tashkent';

    public string $locale = 'ru';

    public $avatar = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->name = $user->name;
        $this->username = $user->username ?? '';
        $this->email = $user->email ?? '';
        $this->phone = $user->phone ?? '';
        $this->countryCode = $user->country_code ?? '';
        $this->city = $user->city ?? '';
        $this->timezone = $user->timezone ?? 'Asia/Tashkent';
        $this->locale = $user->locale ?? 'ru';
    }

    public function save(): void
    {
        $user = Auth::user();
        abort_unless($user !== null, 403);

        $this->validate([
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'username' => ['nullable', 'string', 'min:3', 'max:32', 'regex:/^[a-z0-9_]+$/i', 'unique:users,username,'.$user->id],
            'email' => ['nullable', 'email', 'max:191', 'unique:users,email,'.$user->id],
            'phone' => ['nullable', 'regex:/^\+?\d{9,15}$/', 'unique:users,phone,'.$user->id],
            'countryCode' => ['nullable', 'string', 'size:2'],
            'city' => ['nullable', 'string', 'max:120'],
            'timezone' => ['required', 'string', 'max:64'],
            'locale' => ['required', 'in:ru,uz,en'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $user->fill([
            'name' => $this->name,
            'username' => $this->username !== '' ? mb_strtolower($this->username) : null,
            'email' => $this->email !== '' ? mb_strtolower($this->email) : null,
            'phone' => $this->phone !== '' ? '+'.preg_replace('/\D+/', '', $this->phone) : null,
            'country_code' => $this->countryCode !== '' ? mb_strtoupper($this->countryCode) : null,
            'city' => $this->city ?: null,
            'timezone' => $this->timezone,
            'locale' => $this->locale,
        ])->save();

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');
            $user->forceFill(['avatar_url' => '/storage/'.$path])->save();
            $this->avatar = null;
        }

        session()->flash('status', __('app.profile.saved'));
        $this->dispatch('profile-updated', name: $user->name);
    }

    public function render(): View
    {
        return view('livewire.profile.edit-general-profile', [
            'user' => Auth::user(),
        ]);
    }
}
