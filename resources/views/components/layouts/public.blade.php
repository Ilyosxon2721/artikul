@props(['title' => null, 'metaDescription' => null])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ? $title.' — Artikul' : 'Artikul — '.__('app.tagline') }}</title>
    <meta name="description" content="{{ $metaDescription ?? __('app.tagline') }}">

    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?? 'Artikul' }}">
    <meta property="og:description" content="{{ $metaDescription ?? __('app.tagline') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:site_name" content="Artikul">

    <link rel="canonical" href="{{ url()->current() }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-900">
    <header class="border-b border-gray-100 bg-white sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="text-xl font-bold text-blue-700 tracking-tight">Artikul</a>
            <nav class="hidden md:flex items-center gap-6 text-sm text-gray-600">
                <a href="{{ route('tasks.index') }}" class="hover:text-gray-900">{{ __('app.nav.tasks') }}</a>
                <a href="{{ route('sellers.index') }}" class="hover:text-gray-900">{{ __('app.nav.sellers') }}</a>
                <a href="{{ url('/how-it-works') }}" class="hover:text-gray-900">{{ __('app.nav.how_it_works') }}</a>
            </nav>
            <div class="flex items-center gap-3 text-sm">
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-gray-900">{{ __('app.nav.dashboard') }}</a>
                @endauth
                @guest
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-gray-900">{{ __('app.nav.login') }}</a>
                    <a href="{{ route('register') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">{{ __('app.nav.register') }}</a>
                @endguest
            </div>
        </div>
    </header>

    {{ $slot }}

    <footer class="border-t border-gray-100 bg-gray-50 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 grid grid-cols-1 md:grid-cols-4 gap-8 text-sm">
            <div>
                <div class="text-lg font-bold text-blue-700">Artikul</div>
                <p class="text-gray-500 mt-2">{{ __('app.tagline') }}</p>
                <p class="text-xs text-gray-400 mt-3">© {{ date('Y') }} Artikul</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">{{ __('app.footer.platform') }}</h3>
                <ul class="space-y-1 text-gray-500">
                    <li><a href="{{ route('tasks.index') }}" class="hover:text-gray-800">{{ __('app.nav.tasks') }}</a></li>
                    <li><a href="{{ route('sellers.index') }}" class="hover:text-gray-800">{{ __('app.nav.sellers') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">{{ __('app.footer.company') }}</h3>
                <ul class="space-y-1 text-gray-500">
                    <li><a href="{{ url('/about') }}" class="hover:text-gray-800">{{ __('app.footer.about') }}</a></li>
                    <li><a href="{{ url('/terms') }}" class="hover:text-gray-800">{{ __('app.footer.terms') }}</a></li>
                    <li><a href="{{ url('/privacy') }}" class="hover:text-gray-800">{{ __('app.footer.privacy') }}</a></li>
                </ul>
            </div>
            <div>
                <h3 class="font-semibold text-gray-700 mb-2">{{ __('app.footer.contacts') }}</h3>
                <ul class="space-y-1 text-gray-500">
                    <li><a href="mailto:hello@artikul.uz" class="hover:text-gray-800">hello@artikul.uz</a></li>
                </ul>
            </div>
        </div>
    </footer>
</body>
</html>
