<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $store['name'] }} - Profil Toko Jastiper | JastipKampung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FDF8F5] text-gray-800 antialiased selection:bg-[#B5502E] selection:text-white">
    @auth
        @include('layouts.navigation')
    @else
        <nav class="sticky top-0 z-50 bg-[#FEFAF7] px-6 py-4 flex flex-wrap justify-between items-center border-b border-[#EADBD1] gap-y-4">
            <a href="/" class="flex items-center gap-2 hover:opacity-80 transition">
                <div class="bg-[#B5502E] text-white p-1 rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <span class="font-bold text-[15px] text-gray-800 tracking-tight">Jastip<span class="text-[#B5502E]">Kampung</span></span>
            </a>
            <div class="flex items-center gap-6 font-bold text-[14px]">
                <a href="/" class="text-gray-600 hover:text-gray-900 pb-0.5">Home</a>
                <a href="{{ route('katalog') }}" class="text-gray-600 hover:text-gray-900 pb-0.5">Katalog PO</a>
                <a href="{{ route('register', ['role' => 'jastiper']) }}" class="text-gray-600 hover:text-gray-900 pb-0.5">Jadi Jastiper</a>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 pb-0.5 ml-2">Masuk</a>
                <a href="{{ route('register') }}" class="bg-[#B5502E] text-white px-4 py-2 rounded-lg hover:bg-[#9E4527] transition shadow-sm">Daftar</a>
            </div>
        </nav>
    @endauth

    <div class="mx-auto w-full max-w-7xl px-4 pt-5 sm:px-6 lg:px-8">
        <a href="{{ Auth::check() ? route('katalog') : url('/') }}" onclick="if (document.referrer && new URL(document.referrer).origin === window.location.origin) { event.preventDefault(); window.history.back(); }" class="inline-flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold text-[#8C5D42] transition hover:bg-[#F5EBE4] hover:text-[#B5502E]">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6M9 12h12" /></svg>
            Kembali
        </a>
    </div>

    <main class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8 space-y-10">
        @if (session('status'))
            <div role="status" class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
        @endif

        <!-- SHOPEE-STYLE STORE HEADER CARD -->
        <section class="overflow-hidden rounded-3xl border border-[#EADBD1] bg-white shadow-sm">
            <div class="grid grid-cols-1 lg:grid-cols-12">
                <!-- Left Store Identity Banner -->
                <div class="lg:col-span-5 bg-gradient-to-br from-[#B5502E] to-[#8F3B1E] p-6 sm:p-8 text-white flex flex-col justify-between gap-6">
                    <div class="flex items-start gap-4">
                        <div class="relative shrink-0">
                            <div class="flex h-20 w-20 items-center justify-center rounded-full border-2 border-white/80 bg-white/15 text-2xl font-extrabold text-white shadow-inner">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($store['name'], 0, 1)) }}
                            </div>
                            <span class="absolute -bottom-1.5 left-1/2 -translate-x-1/2 whitespace-nowrap rounded-full bg-amber-400 px-2.5 py-0.5 text-[10px] font-extrabold uppercase tracking-wide text-gray-900 shadow">
                                KTM Verified
                            </span>
                        </div>
                        <div class="min-w-0">
                            <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-white/80">Toko Jastiper Mahasiswa</p>
                            <h1 class="mt-1 text-2xl font-extrabold leading-tight truncate">{{ $store['name'] }}</h1>
                            <p class="mt-1 text-xs font-semibold text-white/90">{{ $store['faculty'] }} &bull; Rute {{ $store['origin_city'] }}</p>
                            <div class="mt-3 inline-flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1 text-xs font-bold">
                                <span class="text-amber-300">★</span>
                                @if ($store['store_rating'])
                                    <span>{{ number_format($store['store_rating'], 1) }} / 5.0 Rating Toko</span>
                                @else
                                    <span>Jastiper Baru</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <p class="text-xs leading-relaxed text-white/90 border-t border-white/20 pt-4">
                        {{ $store['bio'] }}
                    </p>
                </div>

                <!-- Right Store Metrics Grid (Shopee Style) -->
                <div class="lg:col-span-7 p-6 sm:p-8 flex flex-col justify-center bg-white">
                    <div class="mb-4 flex items-center justify-between border-b border-[#F5EBE4] pb-3">
                        <h2 class="text-xs font-extrabold uppercase tracking-[0.16em] text-[#8C5D42]">Performa & Kepercayaan Toko</h2>
                        <span class="rounded-full bg-[#F5EBE4] px-3 py-1 text-[11px] font-bold text-[#B5502E]">Terverifikasi Komunitas UNNES</span>
                    </div>

                    <dl class="grid grid-cols-2 sm:grid-cols-3 gap-y-5 gap-x-6 text-sm">
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Penilaian Toko</dt>
                            <dd class="mt-1 flex items-center gap-1.5 font-extrabold text-[#B5502E] text-base">
                                <span class="text-amber-500">★</span>
                                @if ($store['store_rating'])
                                    {{ number_format($store['store_rating'], 1) }} <span class="text-xs font-semibold text-gray-500">({{ $store['total_reviews'] }} Penilaian)</span>
                                @else
                                    <span class="text-sm text-gray-700">Belum ada</span>
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Rata-rata Produk</dt>
                            <dd class="mt-1 flex items-center gap-1 font-extrabold text-gray-900 text-base">
                                <span class="text-amber-500">★</span>
                                {{ $store['product_rating'] ? number_format($store['product_rating'], 1).' / 5.0' : 'Baru' }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Titipan Selesai</dt>
                            <dd class="mt-1 font-extrabold text-gray-900 text-base">{{ $store['completed_orders'] }} <span class="text-xs font-semibold text-gray-500">pesanan</span></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Etalase PO Aktif</dt>
                            <dd class="mt-1 font-extrabold text-[#B5502E] text-base">{{ $store['active_products_count'] }} <span class="text-xs font-semibold text-gray-500">rute</span></dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Ketepatan Jadwal</dt>
                            <dd class="mt-1 font-extrabold text-emerald-700 text-base">{{ $store['on_time_rate'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-medium text-gray-500">Bergabung</dt>
                            <dd class="mt-1 font-extrabold text-gray-800 text-base">{{ $store['joined_label'] }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </section>

        <!-- SECTION 1: ETALASE PO / PRODUK AKTIF JASTIPER -->
        <section>
            <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#B5502E]">Etalase Jastiper</p>
                    <h2 class="mt-1 text-2xl font-extrabold text-gray-900">Pre-Order Aktif dari {{ $store['name'] }}</h2>
                </div>
                <span class="text-xs font-semibold text-gray-500">{{ count($store['products']) }} penawaran tersedia</span>
            </div>

            @if (empty($store['products']))
                <div class="rounded-2xl border border-dashed border-[#DCC4B5] bg-white px-6 py-12 text-center">
                    <p class="font-bold text-gray-800">Belum ada rute PO yang sedang dibuka saat ini.</p>
                    <p class="mt-1 text-xs text-gray-500">Cek kembali menjelang akhir pekan atau lihat rute jastiper lainnya di katalog.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($store['products'] as $product)
                        <article class="bg-white border border-[#EADBD1] rounded-2xl p-5 hover:shadow-md hover:border-[#B5502E] transition flex flex-col h-full">
                            <div class="flex justify-between items-start mb-2.5">
                                <span class="text-xs font-extrabold text-gray-800">{{ $product['rute'] }}</span>
                                <span class="text-[10px] font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 rounded">Sisa {{ $product['slot'] }} slot</span>
                            </div>

                            <h3 class="font-extrabold text-lg text-gray-900 leading-snug mb-2">{{ $product['title'] }}</h3>

                            <!-- Shopee-style Product Rating Row -->
                            <div class="mb-4 flex flex-wrap items-center gap-2 text-xs">
                                @if (!empty($product['product_reviews_count']) && $product['product_rating'])
                                    <span class="inline-flex items-center gap-1 rounded-md bg-amber-50 border border-amber-200 px-2 py-0.5 font-extrabold text-amber-800">
                                        <span class="text-amber-500">★</span> {{ number_format($product['product_rating'], 1) }}
                                    </span>
                                    <span class="text-gray-500 font-semibold">{{ $product['product_reviews_count'] }} penilaian</span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-md bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 font-bold text-[#8C5D42]">★ PO Baru</span>
                                @endif
                                <span class="text-gray-300">|</span>
                                <span class="text-gray-600 font-semibold">{{ $product['sold_count'] }} terjual</span>
                            </div>

                            @if (!empty($product['description']))
                                <p class="mb-4 rounded-lg bg-[#FDF8F5] p-3 text-xs leading-relaxed text-gray-600">{{ $product['description'] }}</p>
                            @endif

                            <div class="mb-4">
                                <div class="flex justify-between text-[11px] font-bold mb-1.5">
                                    <span class="text-gray-600">Kuota Terisi</span>
                                    <span class="text-gray-900">{{ $product['terisi'] }}/{{ $product['total'] }} {{ $product['satuan_kuota'] }}</span>
                                </div>
                                <div class="w-full bg-[#F5EBE4] h-1.5 rounded-full overflow-hidden">
                                    @php $percentage = min(100, ($product['terisi'] / max(1, $product['total'])) * 100); @endphp
                                    <div class="bg-[#B5502E] h-full rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>

                            <div class="space-y-1.5 mb-4 text-[11px] flex-grow">
                                <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                                    <span class="text-gray-500">Countdown:</span>
                                    <span class="font-bold text-[#B5502E]">PO Ditutup: {{ $product['countdown'] }}</span>
                                </div>
                                <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                                    <span class="text-gray-500">Jadwal Kembali:</span>
                                    <span class="font-bold text-gray-800">{{ $product['kembali'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Titik Kumpul:</span>
                                    <span class="font-bold text-gray-800">{{ $product['kumpul'] }}</span>
                                </div>
                            </div>

                            <div class="mt-auto pt-3 border-t border-[#EADBD1] mb-4">
                                <div class="flex justify-between items-end">
                                    <span class="text-gray-500 text-[11px]">Harga + Jastip:</span>
                                    <span class="font-extrabold text-lg text-[#B5502E]">Rp {{ number_format($product['harga'] + $product['fee'], 0, ',', '.') }} <span class="text-[10px] font-medium text-gray-500">/ {{ $product['satuan_harga'] }}</span></span>
                                </div>
                            </div>

                            @auth
                                @if (Auth::user()->role === 'pembeli')
                                    <form method="POST" action="{{ route('cart.add') }}" class="flex gap-2">
                                        @csrf
                                        <input type="hidden" name="type" value="{{ $product['type'] }}">
                                        <input type="hidden" name="id" value="{{ $product['type'] === 'trip' ? $product['trip_id'] : $product['id'] }}">
                                        <input type="number" name="quantity" min="1" max="{{ max(1, min(20, $product['slot'])) }}" value="1" required class="w-16 rounded-lg border border-[#EADBD1] text-sm focus:border-[#B5502E] focus:ring-[#B5502E]" {{ $product['slot'] < 1 ? 'disabled' : '' }}>
                                        <button type="submit" class="flex-1 rounded-lg bg-[#B5502E] py-2.5 text-sm font-bold text-white hover:bg-[#9E4527] transition disabled:opacity-50" {{ $product['slot'] < 1 ? 'disabled' : '' }}>
                                            {{ $product['slot'] < 1 ? 'Kuota Penuh' : '+ Keranjang' }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('katalog') }}" class="block w-full rounded-lg border border-[#EADBD1] py-2.5 text-center text-xs font-bold text-gray-700 hover:bg-[#FDF8F5]">Lihat di Katalog</a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="block w-full rounded-lg bg-[#B5502E] py-2.5 text-center text-sm font-bold text-white hover:bg-[#9E4527] transition">Masuk untuk Ikut PO</a>
                            @endauth
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- SECTION 2: PENILAIAN TOKO & PRODUK (SHOPEE RATING BREAKDOWN) -->
        <section id="ulasan" class="rounded-3xl border border-[#EADBD1] bg-white p-6 sm:p-8 shadow-sm">
            <div class="mb-6">
                <p class="text-xs font-extrabold uppercase tracking-[0.18em] text-[#B5502E]">Ulasan Pembeli Terverifikasi</p>
                <h2 class="mt-1 text-2xl font-extrabold text-gray-900">Penilaian Produk & Toko Jastiper</h2>
            </div>

            <!-- Summary Box like Shopee -->
            <div class="rounded-2xl border border-[#EADBD1] bg-[#FDF8F5] p-5 sm:p-6 mb-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    <!-- Overall Score -->
                    <div class="lg:col-span-3 text-center lg:border-r lg:border-[#EADBD1] lg:pr-6">
                        <div class="flex items-baseline justify-center gap-1 text-[#B5502E]">
                            <span class="text-4xl font-extrabold">{{ $store['store_rating'] ? number_format($store['store_rating'], 1) : '—' }}</span>
                            <span class="text-sm font-bold text-gray-600">dari 5</span>
                        </div>
                        <div class="mt-1.5 flex justify-center gap-0.5 text-lg text-amber-500" aria-hidden="true">
                            @for ($i = 1; $i <= 5; $i++)
                                <span>{{ ($store['store_rating'] ?? 0) >= $i - 0.3 ? '★' : '☆' }}</span>
                            @endfor
                        </div>
                        <p class="mt-1 text-xs font-semibold text-gray-500">{{ $store['total_reviews'] }} penilaian pembeli</p>
                    </div>

                    <!-- 5-Star Bars -->
                    <div class="lg:col-span-4 space-y-1.5">
                        @foreach ([5, 4, 3, 2, 1] as $star)
                            @php $row = $store['star_breakdown'][$star] ?? ['count' => 0, 'percent' => 0]; @endphp
                            <div class="flex items-center gap-2.5 text-xs">
                                <span class="w-12 font-bold text-gray-700 flex items-center gap-0.5">{{ $star }} <span class="text-amber-500">★</span></span>
                                <div class="flex-1 h-2 rounded-full bg-[#EADBD1]/70 overflow-hidden">
                                    <div class="h-full rounded-full bg-amber-500" style="width: {{ $row['percent'] }}%"></div>
                                </div>
                                <span class="w-10 text-right font-semibold text-gray-500">{{ $row['count'] }}</span>
                            </div>
                        @endforeach
                    </div>

                    <!-- Filter Buttons like Shopee -->
                    <div class="lg:col-span-5 flex flex-wrap gap-2">
                        @php
                            $filterPills = [
                                'semua' => 'Semua ('.$store['total_reviews'].')',
                                '5' => '5 Bintang ('.($store['star_breakdown'][5]['count'] ?? 0).')',
                                '4' => '4 Bintang ('.($store['star_breakdown'][4]['count'] ?? 0).')',
                                '3' => '3 Bintang ('.($store['star_breakdown'][3]['count'] ?? 0).')',
                                '2' => '2 Bintang ('.($store['star_breakdown'][2]['count'] ?? 0).')',
                                '1' => '1 Bintang ('.($store['star_breakdown'][1]['count'] ?? 0).')',
                                'komentar' => 'Dengan Komentar ('.$withCommentCount.')',
                            ];
                        @endphp
                        @foreach ($filterPills as $key => $label)
                            <a href="{{ route('jastiper.profile', ['slug' => $store['slug'], 'star' => $key]) }}#ulasan"
                               class="rounded-lg px-3.5 py-2 text-xs font-bold transition {{ $starFilter === (string) $key ? 'border-2 border-[#B5502E] bg-white text-[#B5502E] shadow-sm' : 'border border-[#EADBD1] bg-white text-gray-700 hover:border-[#B5502E]' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Review List -->
            <div class="divide-y divide-[#F5EBE4]">
                @forelse ($reviews as $review)
                    <article class="py-5 first:pt-0 last:pb-0">
                        <div class="flex items-start gap-3.5">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#F5EBE4] text-sm font-extrabold text-[#B5502E]">
                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($review['buyer_name'], 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <span class="text-sm font-extrabold text-gray-900">{{ $review['buyer_name'] }}</span>
                                        <span class="ml-1.5 rounded bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 text-[10px] font-bold text-[#8C5D42]">{{ $review['buyer_faculty'] }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400 font-medium">{{ $review['date'] }}</span>
                                </div>

                                <!-- Dual Rating Pills (Product + Store) -->
                                <div class="mt-1.5 flex flex-wrap items-center gap-3 text-xs">
                                    <span class="inline-flex items-center gap-1 font-bold text-amber-600">
                                        @for ($i = 1; $i <= 5; $i++)<span>{{ $i <= $review['product_rating'] ? '★' : '☆' }}</span>@endfor
                                        <span class="text-gray-600 font-semibold ml-0.5">Kualitas Produk ({{ $review['product_rating'] }}/5)</span>
                                    </span>
                                    <span class="text-gray-300">&bull;</span>
                                    <span class="inline-flex items-center gap-1 font-bold text-[#B5502E]">
                                        <span>★ {{ $review['store_rating'] }}/5</span>
                                        <span class="text-gray-600 font-semibold">Pelayanan Jastiper</span>
                                    </span>
                                </div>

                                <p class="mt-2 inline-block rounded-md bg-[#FDF8F5] px-2.5 py-1 text-[11px] font-semibold text-gray-500">
                                    Variasi / PO: <span class="font-bold text-gray-700">{{ $review['product_title'] }}</span>
                                </p>

                                @if (!empty($review['comment']))
                                    <p class="mt-2.5 text-sm leading-relaxed text-gray-700">{{ $review['comment'] }}</p>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="py-12 text-center">
                        <p class="text-sm font-bold text-gray-700">Belum ada ulasan pada filter bintang ini.</p>
                        @if ($starFilter !== 'semua')
                            <a href="{{ route('jastiper.profile', ['slug' => $store['slug']]) }}#ulasan" class="mt-2 inline-block text-xs font-bold text-[#B5502E] hover:underline">Tampilkan semua ulasan</a>
                        @endif
                    </div>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
