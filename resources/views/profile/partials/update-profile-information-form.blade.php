<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __("Update your account's profile information and email address.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 font-medium text-sm text-green-600">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div>
            <x-input-label for="faculty" value="Fakultas & Angkatan UNNES (Opsional)" />
            <x-text-input id="faculty" name="faculty" type="text" class="mt-1 block w-full" :value="old('faculty', $user->faculty)" placeholder="Contoh: FT UNNES '22" />
            <x-input-error class="mt-2" :messages="$errors->get('faculty')" />
        </div>

        @if ($user->role === 'jastiper')
            <div>
                <x-input-label for="origin_city" value="Kota Asal Rute Utama" />
                <x-text-input id="origin_city" name="origin_city" type="text" class="mt-1 block w-full" :value="old('origin_city', $user->origin_city)" placeholder="Contoh: Solo / Purwokerto" />
                <x-input-error class="mt-2" :messages="$errors->get('origin_city')" />
            </div>

            <div>
                <x-input-label for="store_bio" value="Deskripsi Singkat Toko Jastiper" />
                <textarea id="store_bio" name="store_bio" rows="3" maxlength="500" placeholder="Ceritakan jadwal rutin pulang kampung, cara pengemasan, atau jaminan struk toko asli..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-[#B5502E] focus:ring-[#B5502E] text-sm">{{ old('store_bio', $user->store_bio) }}</textarea>
                <x-input-error class="mt-2" :messages="$errors->get('store_bio')" />
            </div>
        @endif

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
