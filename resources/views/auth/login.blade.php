<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-gray-900">Masuk ke JastipKampung</h2>
        <p class="text-sm text-gray-500 mt-2 font-medium">Selamat datang kembali! Silakan masuk ke akun Anda.</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf
        
        <!-- Email Address -->
        <div class="mb-5">
            <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring focus:ring-[#B5502E]/20 transition shadow-sm text-sm py-2.5">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 font-medium text-xs" />
        </div>

        <!-- Password -->
        <div class="mb-5">
            <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring focus:ring-[#B5502E]/20 transition shadow-sm text-sm py-2.5">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 font-medium text-xs" />
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between mb-8">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-[#B5502E] focus:ring-[#B5502E]/20 shadow-sm cursor-pointer">
                <span class="ml-2 text-sm text-gray-600 font-medium group-hover:text-gray-900 transition">Ingat saya</span>
            </label>
            
            @if (Route::has('password.request'))
                <a class="text-sm text-[#B5502E] hover:text-[#9E4527] font-bold transition" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <!-- Button -->
        <button type="submit" class="relative z-10 w-full bg-[#B5502E] text-white font-bold py-3 rounded-xl shadow-sm hover:bg-[#9E4527] transition hover:shadow-md cursor-pointer">
            Masuk
        </button>
    </form>

    <x-slot name="bottom">
        Belum punya akun? <a href="{{ route('register') }}" class="text-[#B5502E] hover:text-[#9E4527] font-bold transition">Daftar di sini</a>
    </x-slot>
</x-guest-layout>