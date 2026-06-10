@props(['title' => null])
@php
    $brand = \App\Support\Settings::get('brand_name', 'Autoplug');
    $logo = \App\Support\Settings::get('logo');
    $favicon = \App\Support\Settings::get('favicon');
    $primary = \App\Support\Settings::get('primary_color', '#7c3aed');
    $logoHeight = \App\Support\Settings::int('logo_height', 40);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? $brand }}</title>
    @if ($favicon)<link rel="icon" href="{{ asset('storage/'.$favicon) }}">@endif
    <style>
        :root { --brand: {{ $primary }}; }
        input[type="range"] { -webkit-appearance: none; appearance: none; height: 8px; border-radius: 9999px; background: #e5e7eb; }
        input[type="range"]:focus { outline: none; }
        input[type="range"]::-webkit-slider-thumb { -webkit-appearance: none; appearance: none; width: 18px; height: 18px; border-radius: 9999px; background: #7c3aed; border: 3px solid #fff; box-shadow: 0 0 0 1px rgba(124,58,237,.35), 0 1px 3px rgba(0,0,0,.3); cursor: pointer; margin-top: -5px; }
        input[type="range"]::-moz-range-thumb { width: 16px; height: 16px; border-radius: 9999px; background: #7c3aed; border: 3px solid #fff; cursor: pointer; }
        input[type="range"]::-moz-range-track { height: 8px; border-radius: 9999px; background: #e5e7eb; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col bg-gray-100 text-gray-900">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="{{ url('/') }}" class="flex items-center gap-2">
                @if ($logo)
                    <img src="{{ asset('storage/'.$logo) }}" alt="{{ $brand }}" style="height: {{ $logoHeight }}px; width:auto;">
                @else
                    <span class="text-lg font-bold">{{ $brand }}</span>
                @endif
            </a>
            <nav class="text-sm flex items-center gap-4">
                <a href="{{ route('garages.index') }}" class="text-gray-600 hover:text-gray-900">Garages</a>
                <a href="{{ route('search') }}" class="text-gray-600 hover:text-gray-900">Find a garage</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="text-gray-600 hover:text-gray-900">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="px-3.5 py-2 bg-violet-600 text-white font-semibold rounded-lg hover:bg-violet-500">Free Account</a>
                @endauth
            </nav>
        </div>
    </header>

    <main class="flex-1 w-full max-w-7xl mx-auto px-4 py-6">
        {{ $slot }}
    </main>

    <footer class="bg-white border-t border-gray-100 mt-12">
        <div class="max-w-7xl mx-auto px-4 py-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                @if ($logo)
                    <img src="{{ asset('storage/'.$logo) }}" alt="" style="height: 24px; width:auto;">
                @endif
                <span>&copy; {{ date('Y') }} {{ $brand }}. All rights reserved.</span>
            </div>
            <nav class="flex gap-4 text-sm text-gray-500">
                <a href="{{ route('garages.index') }}" class="hover:text-gray-900">Garages</a>
                <a href="{{ route('search') }}" class="hover:text-gray-900">Find a garage</a>
                @guest
                    <a href="{{ route('login') }}" class="hover:text-gray-900">Log in</a>
                    <a href="{{ route('register') }}" class="hover:text-gray-900">Create account</a>
                @endguest
            </nav>
        </div>
    </footer>
</body>
</html>
