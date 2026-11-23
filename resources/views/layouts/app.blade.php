<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'JastipKampung') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        
        <style> body { font-family: 'Plus Jakarta Sans', sans-serif; } </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-800 bg-[#FDF8F5] selection:bg-[#B5502E] selection:text-white">
        <div class="min-h-screen">
            @include('layouts.navigation')

            @php
                $backUrl = route('katalog');
                $forceBackFallback = false;

                if (request()->routeIs('katalog')) {
                    $backUrl = url('/');
                    $forceBackFallback = true;
                } elseif (request()->routeIs('cart.*', 'checkout.index')) {
                    $backUrl = route('katalog');
                    $forceBackFallback = true;
                } elseif (request()->routeIs('payment.*')) {
                    $backUrl = route('pesanan.index');
                    $forceBackFallback = true;
                } elseif (request()->routeIs('pesanan.*')) {
                    $backUrl = route('katalog');
                    $forceBackFallback = true;
                } elseif (request()->routeIs('jastiper.trips.create')) {
                    $backUrl = route('dashboard.jastiper');
                } elseif (request()->routeIs('profile.*')) {
                    $backUrl = Auth::user()->role === 'jastiper' ? route('dashboard.jastiper') : route('dashboard.pembeli');
                }
            @endphp

            <div class="mx-auto w-full max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
                <a href="{{ $backUrl }}" data-force-fallback="{{ $forceBackFallback ? 'true' : 'false' }}" onclick="if (this.dataset.forceFallback !== 'true' && document.referrer && new URL(document.referrer).origin === window.location.origin) { event.preventDefault(); window.history.back(); }" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold text-[#8C5D42] transition hover:bg-[#F5EBE4] hover:text-[#B5502E] focus:outline-none focus:ring-2 focus:ring-[#B5502E]/30">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6M9 12h12" /></svg>
                    Kembali
                </a>
            </div>

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
