<?php

use App\Livewire\Actions\Logout;
use Livewire\Volt\Component;

new class extends Component
{
    public function logout(Logout $logout): void
    {
        $logout();

        $this->redirect('/', navigate: true);
    }
}; ?>

<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('home') }}" wire:navigate class="text-xl font-bold text-blue-700 tracking-tight">Artikul</a>
                </div>

                <div class="hidden space-x-6 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')" wire:navigate>
                        {{ __('app.nav.tasks') }}
                    </x-nav-link>
                    <x-nav-link :href="route('sellers.index')" :active="request()->routeIs('sellers.*') || request()->routeIs('public.seller')" wire:navigate>
                        {{ __('app.nav.sellers') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                        {{ __('app.nav.dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-3">
                <livewire:mode-switcher />

                <div class="text-xs text-gray-500 flex items-center gap-1">
                    @foreach (config('app.available_locales', ['ru', 'uz', 'en']) as $code)
                        <a href="{{ route('locale.switch', $code) }}" class="px-1.5 py-0.5 rounded uppercase {{ app()->getLocale() === $code ? 'bg-blue-600 text-white' : 'text-gray-500 hover:bg-gray-100' }}">{{ $code }}</a>
                    @endforeach
                </div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-600 bg-white hover:text-gray-900 focus:outline-none">
                            @if (auth()->user()?->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" alt="" class="w-7 h-7 rounded-full object-cover" />
                            @else
                                <div class="w-7 h-7 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">
                                    {{ mb_strtoupper(mb_substr(auth()->user()?->name ?? '?', 0, 1)) }}
                                </div>
                            @endif
                            <div x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name" x-on:profile-updated.window="name = $event.detail.name" class="ms-2"></div>
                            <svg class="fill-current h-4 w-4 ms-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.general')" wire:navigate>
                            {{ __('app.nav.profile_general') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.buyer.edit')" wire:navigate>
                            {{ __('app.nav.profile_buyer') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.seller.edit')" wire:navigate>
                            {{ __('app.nav.profile_seller') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.portfolio')" wire:navigate>
                            {{ __('app.portfolio.title') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.services')" wire:navigate>
                            {{ __('app.services.title') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.notification-prefs')" wire:navigate>
                            {{ __('app.notifications_prefs.title') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('saved-searches.index')" wire:navigate>
                            {{ __('app.saved_searches.my_title') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('profile.two-factor')" wire:navigate>
                            {{ __('app.two_factor.title') }}
                        </x-dropdown-link>

                        <div class="border-t border-gray-100 my-1"></div>

                        <button wire:click="logout" class="w-full text-start">
                            <x-dropdown-link>
                                {{ __('app.nav.logout') }}
                            </x-dropdown-link>
                        </button>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.*')" wire:navigate>{{ __('app.nav.tasks') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('sellers.index')" :active="request()->routeIs('sellers.*')" wire:navigate>{{ __('app.nav.sellers') }}</x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')" wire:navigate>
                {{ __('app.nav.dashboard') }}
            </x-responsive-nav-link>
        </div>

        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800" x-data="{{ json_encode(['name' => auth()->user()->name]) }}" x-text="name"></div>
                <div class="font-medium text-sm text-gray-500">{{ auth()->user()->email ?: auth()->user()->phone }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.general')" wire:navigate>{{ __('app.nav.profile_general') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.buyer.edit')" wire:navigate>{{ __('app.nav.profile_buyer') }}</x-responsive-nav-link>
                <x-responsive-nav-link :href="route('profile.seller.edit')" wire:navigate>{{ __('app.nav.profile_seller') }}</x-responsive-nav-link>

                <button wire:click="logout" class="w-full text-start">
                    <x-responsive-nav-link>{{ __('app.nav.logout') }}</x-responsive-nav-link>
                </button>
            </div>
        </div>
    </div>
</nav>
