<x-guest-layout>
    <div class="text-center mb-8">
        <h2 class="text-2xl font-extrabold text-gray-900">Daftar ke JastipKampung</h2>
        <p class="text-sm text-gray-500 mt-2 font-medium">Pilih peran Anda dan lengkapi data diri di bawah ini.</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Pilihan Role (Custom Radio UI) -->
        <div class="mb-6">
            <label class="block text-sm font-bold text-gray-700 mb-3">Pilih Peran Akun</label>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- Option Pembeli -->
                <label class="group relative cursor-pointer block h-full">
                    <input type="radio" name="role" value="pembeli" class="sr-only" {{ old('role', request('role') === 'jastiper' ? 'jastiper' : 'pembeli') === 'pembeli' ? 'checked' : '' }}>
                    <div class="h-full p-4 rounded-xl border-2 border-[#EADBD1] group-has-[:checked]:border-[#B5502E] group-has-[:checked]:bg-[#FDF8F5] hover:border-[#B5502E]/50 transition">
                        <div class="flex items-center gap-3 mb-1.5">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 group-has-[:checked]:border-[#B5502E] group-has-[:checked]:bg-[#B5502E] flex items-center justify-center shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 group-has-[:checked]:opacity-100 transition"></div>
                            </div>
                            <span class="font-bold text-gray-900 text-sm">Daftar sebagai Pembeli</span>
                        </div>
                        <p class="text-[11px] text-gray-500 font-medium ml-7 leading-relaxed">Ingin titip makanan kampung halaman</p>
                    </div>
                </label>

                <!-- Option Jastiper -->
                <label class="group relative cursor-pointer block h-full">
                    <input type="radio" name="role" value="jastiper" class="sr-only" {{ old('role', request('role') === 'jastiper' ? 'jastiper' : 'pembeli') === 'jastiper' ? 'checked' : '' }}>
                    <div class="h-full p-4 rounded-xl border-2 border-[#EADBD1] group-has-[:checked]:border-[#B5502E] group-has-[:checked]:bg-[#FDF8F5] hover:border-[#B5502E]/50 transition">
                        <div class="flex items-center gap-3 mb-1.5">
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 group-has-[:checked]:border-[#B5502E] group-has-[:checked]:bg-[#B5502E] flex items-center justify-center shrink-0">
                                <div class="w-1.5 h-1.5 rounded-full bg-white opacity-0 group-has-[:checked]:opacity-100 transition"></div>
                            </div>
                            <span class="font-bold text-gray-900 text-sm">Daftar sebagai Jastiper</span>
                        </div>
                        <p class="text-[11px] text-gray-500 font-medium ml-7 leading-relaxed">Pulang kampung dan mau bawa titipan</p>
                    </div>
                </label>
                
            </div>
            <x-input-error :messages="$errors->get('role')" class="mt-2 text-red-600 font-medium text-xs" />
        </div>

        <!-- Nama Lengkap -->
        <div class="mb-5">
            <label for="name" class="block text-sm font-bold text-gray-700 mb-1.5">Nama Lengkap</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring focus:ring-[#B5502E]/20 transition shadow-sm text-sm py-2.5">
            <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 font-medium text-xs" />
        </div>

        <!-- Email Address -->
        <div class="mb-5">
            <label for="email" class="block text-sm font-bold text-gray-700 mb-1.5">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring focus:ring-[#B5502E]/20 transition shadow-sm text-sm py-2.5">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 font-medium text-xs" />
        </div>

        <!-- Password Row -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
            <!-- Password -->
            <div>
                <label for="password" class="block text-sm font-bold text-gray-700 mb-1.5">Password</label>
                <input id="password" type="password" name="password" required autocomplete="new-password" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring focus:ring-[#B5502E]/20 transition shadow-sm text-sm py-2.5">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 font-medium text-xs" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-1.5">Konfirmasi Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring focus:ring-[#B5502E]/20 transition shadow-sm text-sm py-2.5">
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 font-medium text-xs" />
            </div>
        </div>

        <!-- Button -->
        <button type="submit" class="w-full bg-[#B5502E] text-white font-bold py-3 rounded-xl shadow-sm hover:bg-[#9E4527] transition hover:shadow-md">
            Daftar
        </button>
    </form>

    <x-slot name="bottom">
        Sudah punya akun? <a href="{{ route('login') }}" class="text-[#B5502E] hover:text-[#9E4527] font-bold transition">Masuk di sini</a>
    </x-slot>
</x-guest-layout>
