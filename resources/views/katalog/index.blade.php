<x-app-layout>
    <div class="py-12 md:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if (session('status'))
            <div role="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->has('cart'))
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first('cart') }}</div>
        @endif
        
        <!-- Header -->
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 mb-2">Katalog Pre-Order</h1>
                <p class="text-gray-600 text-sm font-medium">Semua rute & jadwal titip yang aktif minggu ini</p>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium bg-white px-3 py-1.5 rounded border border-[#EADBD1]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Update status otomatis setiap 30 menit
            </div>
        </div>

        <!-- Filters & Search -->
        <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4 mb-5">
            <!-- Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('katalog', ['search' => $search, 'sort' => $currentSort ?? 'rekomendasi']) }}" class="{{ $currentFilter === 'semua' ? 'bg-[#B5502E] text-white shadow-sm' : 'bg-[#FDF8F5] hover:bg-[#EADBD1] text-gray-700 border border-[#EADBD1]' }} px-5 py-2 rounded-md text-sm font-bold transition">
                    Semua
                </a>
                @foreach($cities as $city)
                    <a href="{{ route('katalog', ['filter' => $city, 'search' => $search, 'sort' => $currentSort ?? 'rekomendasi']) }}" class="{{ strtolower($currentFilter) === strtolower($city) ? 'bg-[#B5502E] text-white shadow-sm' : 'bg-[#FDF8F5] hover:bg-[#EADBD1] text-gray-700 border border-[#EADBD1]' }} px-5 py-2 rounded-md text-sm font-bold transition">
                        {{ $city }}
                    </a>
                @endforeach
            </div>

            <!-- Search Bar -->
            <form action="{{ route('katalog') }}" method="GET" class="relative w-full lg:w-80">
                <input type="hidden" name="filter" value="{{ $currentFilter }}">
                <input type="hidden" name="sort" value="{{ $currentSort ?? 'rekomendasi' }}">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari makanan, kota, atau jastiper..." class="w-full pl-10 pr-4 py-2 rounded-lg border border-[#EADBD1] focus:border-[#B5502E] focus:ring focus:ring-[#B5502E]/20 text-sm font-medium shadow-sm transition bg-white">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                @if($search)
                    <a href="{{ route('katalog', ['filter' => $currentFilter, 'sort' => $currentSort ?? 'rekomendasi']) }}" class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </a>
                @endif
            </form>
        </div>

        <!-- Shopee-style Sort Bar -->
        <div class="mb-8 flex flex-wrap items-center gap-2 rounded-xl border border-[#EADBD1] bg-[#FEFAF7] px-4 py-2.5 text-xs">
            <span class="font-bold text-gray-500 mr-1">Urutkan:</span>
            @php
                $activeSort = $currentSort ?? 'rekomendasi';
                $sortOptions = [
                    'rekomendasi' => 'Rekomendasi',
                    'rating' => '★ Rating Tertinggi',
                    'terlaris' => 'Terlaris',
                ];
            @endphp
            @foreach ($sortOptions as $sortKey => $sortLabel)
                <a href="{{ route('katalog', ['filter' => $currentFilter, 'search' => $search, 'sort' => $sortKey]) }}"
                   class="rounded-md px-3 py-1.5 font-bold transition {{ $activeSort === $sortKey ? 'bg-[#B5502E] text-white shadow-sm' : 'bg-white border border-[#EADBD1] text-gray-700 hover:border-[#B5502E]' }}">
                    {{ $sortLabel }}
                </a>
            @endforeach
        </div>

        <!-- Grid Cards -->
        @if($products->isEmpty())
            <!-- Empty State -->
            <div class="py-16 flex flex-col items-center justify-center text-center bg-white border border-[#EADBD1] rounded-2xl shadow-sm">
                <div class="w-16 h-16 bg-[#FDF8F5] text-[#B5502E] rounded-full flex items-center justify-center mb-4 border border-[#EADBD1]">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                </div>
                <h3 class="text-xl font-extrabold text-gray-900 mb-2">Belum ada jadwal PO</h3>
                <p class="text-sm text-gray-500 font-medium">Coba ubah kata kunci pencarian atau pilih kota asal lainnya.</p>
                <a href="{{ route('katalog') }}" class="mt-6 text-[#B5502E] font-bold hover:underline text-sm">Reset Filter Pencarian</a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach($products as $product)
                    <div class="bg-white border border-[#EADBD1] rounded-xl p-5 hover:shadow-lg hover:border-[#B5502E] transition duration-300 flex flex-col h-full group">
                        <div class="flex justify-between items-start mb-3">
                            <div class="text-xs font-extrabold text-gray-800">{{ $product['rute'] }}</div>
                            <div class="text-[10px] font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 rounded">Sisa {{ $product['slot'] }} slot</div>
                        </div>
                        <h3 class="font-extrabold text-lg leading-snug mb-2 group-hover:text-[#B5502E] transition">{{ $product['title'] }}</h3>

                        <!-- Shopee-style Product Star Rating & Sold Count -->
                        <div class="mb-3 flex flex-wrap items-center gap-1.5 text-[11px]">
                            @if (!empty($product['product_reviews_count']) && $product['product_rating'])
                                <span class="inline-flex items-center gap-0.5 rounded bg-amber-50 border border-amber-200 px-1.5 py-0.5 font-extrabold text-amber-800">
                                    <span class="text-amber-500">★</span> {{ number_format($product['product_rating'], 1) }}
                                </span>
                                <a href="{{ route('jastiper.profile', $product['jastiper_slug']) }}#ulasan" class="font-semibold text-gray-500 hover:text-[#B5502E] hover:underline">({{ $product['product_reviews_count'] }} penilaian)</a>
                            @else
                                <span class="inline-flex items-center gap-0.5 rounded bg-[#FDF8F5] border border-[#EADBD1] px-1.5 py-0.5 font-bold text-[#8C5D42]">★ PO Baru</span>
                            @endif
                            <span class="text-gray-300">|</span>
                            <span class="font-semibold text-gray-600">{{ $product['sold_count'] ?? 0 }} terjual</span>
                        </div>

                        <!-- Clickable Jastiper Store Badge -->
                        <a href="{{ route('jastiper.profile', $product['jastiper_slug']) }}" class="mb-4 flex items-center justify-between gap-2 rounded-lg border border-[#F1DED4] bg-[#FDF8F5] px-2.5 py-2 hover:border-[#B5502E] transition">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-[#B5502E] text-[10px] font-extrabold text-white">
                                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($product['jastiper'], 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-extrabold text-gray-900">{{ $product['jastiper'] }}</p>
                                    <p class="truncate text-[10px] text-gray-500">{{ $product['fakultas'] }}</p>
                                </div>
                            </div>
                            <span class="shrink-0 rounded bg-white border border-[#EADBD1] px-1.5 py-0.5 text-[10px] font-bold text-[#B5502E]">
                                @if (!empty($product['store_rating']))
                                    <span class="text-amber-500">★</span> {{ number_format($product['store_rating'], 1) }} Toko
                                @else
                                    Lihat Toko →
                                @endif
                            </span>
                        </a>
                        @if (!empty($product['description']))
                            <p class="mb-4 rounded-lg bg-[#FDF8F5] p-3 text-xs leading-relaxed text-gray-600">{{ $product['description'] }}</p>
                        @endif
                        
                        <div class="mb-5">
                            <div class="flex justify-between text-[11px] font-bold mb-1.5">
                                <span class="text-gray-600">Kuota Terisi</span>
                                <span class="text-gray-900">{{ $product['terisi'] }}/{{ $product['total'] }} {{ $product['satuan_kuota'] }}</span>
                            </div>
                            <div class="w-full bg-[#F5EBE4] h-1.5 rounded-full overflow-hidden">
                                @php $percentage = min(100, ($product['terisi'] / $product['total']) * 100); @endphp
                                <div class="bg-[#B5502E] h-full rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        
                        <div class="space-y-2 mb-5 text-[11px] flex-grow">
                            <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                                <span class="text-gray-500 font-medium">Countdown:</span>
                                <span class="font-bold text-[#B5502E] bg-[#FDF8F5] px-1.5 rounded">PO Ditutup: {{ $product['countdown'] }}</span>
                            </div>
                            <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                                <span class="text-gray-500 font-medium">Jadwal Kembali:</span>
                                <span class="font-bold text-gray-800">{{ $product['kembali'] }}</span>
                            </div>
                            <div class="flex justify-between pb-1.5">
                                <span class="text-gray-500 font-medium">Titik Kumpul:</span>
                                <span class="font-bold text-gray-800">{{ $product['kumpul'] }}</span>
                            </div>
                        </div>
                        
                        <div class="mt-auto pt-4 border-t border-[#EADBD1] mb-5">
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-gray-500 text-[11px] font-medium">Estimasi Harga:</span>
                                <span class="font-extrabold text-lg text-gray-900">Rp {{ number_format($product['harga'], 0, ',', '.') }} <span class="text-[10px] font-medium text-gray-500">/ {{ $product['satuan_harga'] }}</span></span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 text-[11px] font-medium">Biaya Titip:</span>
                                <span class="font-bold text-[#B5502E] text-sm">Rp {{ number_format($product['fee'], 0, ',', '.') }}</span>
                            </div>
                        </div>
                        
                        @if (Auth::user()->role === 'pembeli')
                            <form method="POST" action="{{ route('cart.add') }}" class="space-y-2">
                                @csrf
                                <input type="hidden" name="type" value="{{ $product['type'] }}">
                                <input type="hidden" name="id" value="{{ $product['type'] === 'trip' ? $product['trip_id'] : $product['id'] }}">
                                <label class="sr-only" for="quantity-{{ $product['id'] }}">Jumlah {{ $product['satuan_kuota'] }}</label>
                                <div class="flex gap-2">
                                    <input id="quantity-{{ $product['id'] }}" type="number" name="quantity" min="1" max="{{ max(1, min(20, $product['slot'])) }}" value="1" required class="w-20 rounded-lg border border-[#EADBD1] text-sm focus:border-[#B5502E] focus:ring-[#B5502E]" {{ $product['slot'] < 1 ? 'disabled' : '' }}>
                                    <button type="submit" class="flex-1 border border-[#B5502E] bg-[#B5502E] text-white font-bold py-2.5 rounded-lg hover:bg-[#9E4527] transition text-sm shadow-sm disabled:cursor-not-allowed disabled:opacity-50" {{ $product['slot'] < 1 ? 'disabled' : '' }}>
                                        {{ $product['slot'] < 1 ? 'Kuota Penuh' : 'Tambah ke Pesanan' }}
                                    </button>
                                </div>
                                @error('quantity')
                                    <p class="text-xs font-semibold text-red-600">{{ $message }}</p>
                                @enderror
                            </form>
                        @else
                            <a href="{{ route('dashboard.jastiper') }}" class="block w-full rounded-lg border border-[#EADBD1] bg-[#FDF8F5] py-2.5 text-center text-xs font-bold text-[#8C5D42] hover:border-[#B5502E] hover:text-[#B5502E] transition">
                                Mode Jastiper · Kelola PO di Dashboard
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8 flex justify-center">
                {{ $products->appends(request()->query())->links('pagination::tailwind') }}
            </div>

            @if (session('cart') && count(session('cart')) > 0)
                <div class="fixed inset-x-0 bottom-0 z-30 border-t border-[#EADBD1] bg-white/95 px-4 py-3 shadow-[0_-8px_30px_rgba(62,36,23,0.10)] backdrop-blur sm:px-6">
                    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4">
                        <div>
                            <p class="font-extrabold text-gray-900">{{ collect(session('cart'))->sum('quantity') }} item di keranjang</p>
                            <p class="text-xs text-gray-500">Periksa pesanan sebelum memilih pembayaran.</p>
                        </div>
                        <a href="{{ route('cart.index') }}" class="shrink-0 rounded-lg bg-[#B5502E] px-5 py-3 text-sm font-bold text-white hover:bg-[#9E4527]">Lihat Keranjang <span aria-hidden="true">→</span></a>
                    </div>
                </div>
                <div class="h-20"></div>
            @endif
        @endif
        
    </div>
</x-app-layout>
