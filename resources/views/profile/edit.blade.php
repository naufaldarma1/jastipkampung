<x-app-layout>
    <div class="py-10 sm:py-12">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-[#B5502E]">Pengaturan Akun</p>
                <h1 class="mt-1.5 text-3xl font-extrabold text-gray-900">Profil & Reputasi</h1>
                <p class="mt-1 text-sm text-gray-600">Kelola informasi akun{{ $storeProfile ? ', identitas toko jastiper, dan pantau rating kepercayaan pembeli' : ' dan keamanan kata sandi Anda' }}.</p>
            </div>

            @if ($storeProfile)
                <!-- Shopee-Style Store Rating & Reputation Card for Jastiper -->
                <section class="overflow-hidden rounded-2xl border border-[#EADBD1] bg-white shadow-sm">
                    <div class="flex flex-col gap-4 border-b border-[#F5EBE4] bg-[#FCEAE4]/70 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3.5">
                            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-[#B5502E] text-lg font-extrabold text-white">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <h2 class="text-lg font-extrabold text-gray-900">{{ $user->name }}</h2>
                                    <span class="rounded-full bg-amber-100 px-2.5 py-0.5 text-[10px] font-extrabold uppercase text-amber-900">Toko Jastiper</span>
                                </div>
                                <p class="text-xs font-semibold text-gray-600">{{ $storeProfile['faculty'] }} &bull; Rute {{ $storeProfile['origin_city'] }}</p>
                            </div>
                        </div>
                        <a href="{{ route('jastiper.profile', $storeProfile['slug']) }}" class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-[#B5502E] px-4 py-2.5 text-xs font-bold text-white shadow-sm hover:bg-[#9E4527]">
                            Lihat Halaman Toko Publik →
                        </a>
                    </div>

                    <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-12 items-center">
                        <div class="md:col-span-7 grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="rounded-xl border border-[#F1DED4] bg-[#FDF8F5] p-3.5">
                                <p class="text-[11px] font-semibold text-gray-500">Rating Toko</p>
                                <p class="mt-1 text-xl font-extrabold text-[#B5502E] flex items-center gap-1">
                                    <span class="text-amber-500">★</span>
                                    {{ $storeProfile['store_rating'] ? number_format($storeProfile['store_rating'], 1) : '—' }}
                                </p>
                                <p class="mt-0.5 text-[10px] text-gray-500">dari 5.0 bintang</p>
                            </div>
                            <div class="rounded-xl border border-[#F1DED4] bg-[#FDF8F5] p-3.5">
                                <p class="text-[11px] font-semibold text-gray-500">Rating Produk</p>
                                <p class="mt-1 text-xl font-extrabold text-gray-900 flex items-center gap-1">
                                    <span class="text-amber-500">★</span>
                                    {{ $storeProfile['product_rating'] ? number_format($storeProfile['product_rating'], 1) : '—' }}
                                </p>
                                <p class="mt-0.5 text-[10px] text-gray-500">kualitas titipan</p>
                            </div>
                            <div class="rounded-xl border border-[#F1DED4] bg-[#FDF8F5] p-3.5">
                                <p class="text-[11px] font-semibold text-gray-500">Total Penilaian</p>
                                <p class="mt-1 text-xl font-extrabold text-gray-900">{{ $storeProfile['total_reviews'] }}</p>
                                <p class="mt-0.5 text-[10px] text-gray-500">ulasan pembeli</p>
                            </div>
                            <div class="rounded-xl border border-[#F1DED4] bg-[#FDF8F5] p-3.5">
                                <p class="text-[11px] font-semibold text-gray-500">Titipan Selesai</p>
                                <p class="mt-1 text-xl font-extrabold text-emerald-700">{{ $storeProfile['completed_orders'] }}</p>
                                <p class="mt-0.5 text-[10px] text-gray-500">serah terima</p>
                            </div>
                        </div>

                        <div class="md:col-span-5 space-y-1.5 border-t border-[#F5EBE4] pt-4 md:border-t-0 md:border-l md:pt-0 md:pl-6">
                            <p class="text-[11px] font-extrabold uppercase tracking-wider text-[#8C5D42] mb-2">Distribusi Bintang Toko</p>
                            @foreach ([5, 4, 3, 2, 1] as $star)
                                @php $row = $storeProfile['star_breakdown'][$star] ?? ['count' => 0, 'percent' => 0]; @endphp
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="w-10 font-bold text-gray-700">{{ $star }} <span class="text-amber-500">★</span></span>
                                    <div class="flex-1 h-1.5 rounded-full bg-[#F5EBE4] overflow-hidden">
                                        <div class="h-full rounded-full bg-amber-500" style="width: {{ $row['percent'] }}%"></div>
                                    </div>
                                    <span class="w-8 text-right text-[11px] font-semibold text-gray-500">{{ $row['count'] }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </section>
            @endif

            <div class="p-6 sm:p-8 bg-white border border-[#EADBD1] shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-[#EADBD1] shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white border border-[#EADBD1] shadow-sm rounded-2xl">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
