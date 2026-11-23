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
    <body class="bg-[#FDF8F5] text-gray-800 antialiased selection:bg-[#B5502E] selection:text-white">
        <div class="min-h-screen flex flex-col items-center py-8 px-4">
            <div class="w-full max-w-lg">
                <a href="{{ url('/') }}" onclick="if (document.referrer && new URL(document.referrer).origin === window.location.origin) { event.preventDefault(); window.history.back(); }" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold text-[#8C5D42] transition hover:bg-[#F5EBE4] hover:text-[#B5502E] focus:outline-none focus:ring-2 focus:ring-[#B5502E]/30">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6M9 12h12" /></svg>
                    Kembali
                </a>
            </div>

            <div class="flex w-full flex-1 flex-col items-center justify-center">
            
            <!-- Logo -->
            <a href="/" class="mb-8 flex items-center gap-2 hover:opacity-90 transition">
                <div class="bg-[#B5502E] text-white p-1.5 rounded-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <span class="font-extrabold text-2xl tracking-tight text-gray-900">Jastip<span class="text-[#B5502E]">Kampung</span></span>
            </a>

            <!-- Card -->
            <div class="w-full sm:max-w-lg px-6 py-8 md:p-10 bg-white shadow-xl shadow-[#EADBD1]/40 border border-[#EADBD1] rounded-3xl">
                {{ $slot }}
            </div>

            <!-- Bottom Slot -->
            @if (isset($bottom))
                <div class="mt-8 text-sm font-medium text-gray-600">
                    {{ $bottom }}
                </div>
            @endif
            </div>
        </div>
    </body>
</html>
