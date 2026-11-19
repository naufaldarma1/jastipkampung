<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JastipKampung - Pre-Order Kuliner Mahasiswa</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-[#FDF8F5] text-gray-800 antialiased selection:bg-[#B5502E] selection:text-white">
    @php
        $jadiJastiperUrl = Auth::check()
            ? (Auth::user()->role === 'jastiper' ? route('dashboard.jastiper') : route('jastiper.join'))
            : route('register', ['role' => 'jastiper']);
    @endphp

    <!-- NAVBAR -->
    <nav class="sticky top-0 z-50 bg-[#FEFAF7] px-6 py-4 flex flex-wrap justify-between items-center border-b border-[#EADBD1] gap-y-4">
        <!-- Logo Kiri -->
        <div class="flex items-center">
            <!-- Icon JastipKampung -->
            <div class="flex items-center gap-2">
                <div class="bg-[#B5502E] text-white p-1 rounded-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                </div>
                <span class="font-bold text-[15px] text-gray-800 tracking-tight">Jastip<span class="text-[#B5502E]">Kampung</span></span>
            </div>

        </div>

        <!-- Links Menu -->
        <div class="flex items-center gap-6 font-bold text-[14px]">
            <a href="/" class="text-[#B5502E] border-b-2 border-[#B5502E] pb-0.5">Home</a>
            <a href="{{ route('katalog') }}" class="text-gray-600 hover:text-gray-900 pb-0.5">Katalog PO</a>
            @auth
                @if (Auth::user()->role === 'pembeli')
                    <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-gray-900 pb-0.5">Keranjang <span class="ml-1 rounded-full bg-[#F5EBE4] px-2 py-0.5 text-[11px] text-[#B5502E]">{{ collect(session('cart', []))->sum('quantity') }}</span></a>
                @endif
            @endauth
            <a href="{{ $jadiJastiperUrl }}" class="text-gray-600 hover:text-gray-900 pb-0.5">Jadi Jastiper</a>
            
            @auth
                @php 
                    $dashboardRoute = Auth::user()->role === 'jastiper' ? route('dashboard.jastiper') : route('dashboard.pembeli');
                @endphp
                <a href="{{ $dashboardRoute }}" class="text-gray-600 hover:text-gray-900 pb-0.5">Dashboard</a>
                <!-- Profile / Auth -->
                <a href="{{ $dashboardRoute }}" class="w-9 h-9 bg-[#B5502E] rounded-full flex items-center justify-center text-white hover:bg-[#9E4527] transition shadow-sm ml-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </a>
            @else
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-gray-900 pb-0.5 ml-2">Masuk</a>
                <a href="{{ route('register') }}" class="bg-[#B5502E] text-white px-4 py-2 rounded-lg hover:bg-[#9E4527] transition shadow-sm">Daftar</a>
            @endauth
        </div>
    </nav>

    <!-- TOP BAR NOTIFIKASI -->
    <div class="bg-[#F5EBE4] border-b border-[#EADBD1] px-4 md:px-8 py-2.5 flex flex-col sm:flex-row justify-between items-center text-xs font-medium text-gray-700">
        <div class="flex items-center gap-2 mb-2 sm:mb-0">
            <svg class="w-4 h-4 text-[#B5502E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>Batch pengantaran akhir pekan ini: Solo, Purwokerto, Kudus & Tegal menuju Kampus Sekaran.</span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-gray-500">Cut-off PO terdekat: Sabtu, 12:00 WIB</span>
            <span class="flex items-center gap-1.5 text-[#B5502E] font-bold">
                <span class="w-1.5 h-1.5 rounded-full bg-[#B5502E]"></span>
                14 Slot Tersedia
            </span>
        </div>
    </div>

    <!-- HERO SECTION -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-20 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        <!-- Kiri -->
        <div class="lg:col-span-7 flex flex-col items-start">
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#F5EBE4] border border-[#EADBD1] text-[#B5502E] text-[10px] font-bold tracking-widest uppercase mb-6">
                <span class="w-1.5 h-1.5 rounded-full bg-[#B5502E]"></span>
                Rute Langsung Ke Kampus UNNES Sekaran
            </div>
            
            <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 leading-[1.15] mb-6">
                Jastip Makanan Khas Kampung Halaman <br class="hidden md:block">
                untuk Mahasiswa UNNES
            </h1>
            
            <p class="text-gray-600 text-lg leading-relaxed mb-8 max-w-2xl">
                Menghubungkan mahasiswa perantau di Sekaran dan Gunungpati yang rindu kuliner kampung halaman dengan teman sekampus yang sedang pulang dan punya bagasi lebih.
            </p>
            
            <div class="flex flex-wrap items-center gap-4 w-full">
                <a href="{{ route('katalog') }}" class="bg-[#B5502E] text-white px-7 py-3.5 rounded-lg font-bold shadow-sm hover:bg-[#9E4527] transition">
                    Pesan Makanan Kampung
                </a>
                <a href="{{ $jadiJastiperUrl }}" class="border border-[#B5502E] text-[#B5502E] px-7 py-3.5 rounded-lg font-bold hover:bg-[#F5EBE4] transition">
                    Jadi Jastiper
                </a>
            </div>

            <div class="w-full h-px bg-[#EADBD1] my-8"></div>
            
            <div class="flex items-center gap-2.5 text-sm text-gray-600 font-medium">
                <svg class="w-5 h-5 text-[#B5502E]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span>Titik kumpul serah terima resmi di area kampus UNNES Sekaran & Banaran.</span>
            </div>
        </div>
        
        <!-- Kanan -->
        <div class="lg:col-span-5 relative">
            <div class="bg-white border border-[#EADBD1] rounded-2xl p-6 md:p-8 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-gray-900 tracking-wide text-sm">LOG RUTE AKTIF</h3>
                    <span class="px-2.5 py-1 bg-white border border-[#EADBD1] text-[#B5502E] text-[10px] font-bold rounded uppercase tracking-wider">Minggu Ke-4</span>
                </div>
                
                <div class="space-y-5">
                    <!-- Rute 1 -->
                    <div class="flex justify-between items-center pb-4 border-b border-[#F5EBE4] last:border-0 last:pb-0">
                        <div>
                            <div class="font-bold text-gray-900 mb-0.5">Solo &rarr; Sekaran</div>
                            <div class="text-sm text-gray-500">Serabi & Abon Sapi</div>
                        </div>
                        <span class="text-xs font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2.5 py-1 rounded">4 slot</span>
                    </div>
                    <!-- Rute 2 -->
                    <div class="flex justify-between items-center pb-4 border-b border-[#F5EBE4] last:border-0 last:pb-0">
                        <div>
                            <div class="font-bold text-gray-900 mb-0.5">Purwokerto &rarr; Sekaran</div>
                            <div class="text-sm text-gray-500">Mendoan & Jenang Jaket</div>
                        </div>
                        <span class="text-xs font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2.5 py-1 rounded">2 slot</span>
                    </div>
                    <!-- Rute 3 -->
                    <div class="flex justify-between items-center pb-4 border-b border-[#F5EBE4] last:border-0 last:pb-0">
                        <div>
                            <div class="font-bold text-gray-900 mb-0.5">Kudus &rarr; Sekaran</div>
                            <div class="text-sm text-gray-500">Jenang Mubarok & Lentog</div>
                        </div>
                        <span class="text-xs font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2.5 py-1 rounded">6 slot</span>
                    </div>
                    <!-- Rute 4 -->
                    <div class="flex justify-between items-center pb-4 border-b border-[#F5EBE4] last:border-0 last:pb-0">
                        <div>
                            <div class="font-bold text-gray-900 mb-0.5">Tegal &rarr; Sekaran</div>
                            <div class="text-sm text-gray-500">Tahu Aci & Teh Poci</div>
                        </div>
                        <span class="text-xs font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2.5 py-1 rounded">3 slot</span>
                    </div>
                </div>
                
                <div class="mt-6 pt-4 border-t border-[#EADBD1] flex justify-between items-center text-xs font-bold text-gray-800 bg-[#FDF8F5] p-3 rounded-lg">
                    <span>Sistem Kliring Mahasiswa</span>
                    <span>100% Terverifikasi</span>
                </div>
            </div>
        </div>
    </div>

    <!-- CARA KERJA SECTION -->
    <div class="bg-[#F5EBE4] py-16 md:py-24 border-y border-[#EADBD1]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-6 mb-12">
                <div>
                    <div class="text-[#B5502E] text-[10px] font-bold tracking-widest uppercase mb-3">PANDUAN ALUR TRANSAKSI</div>
                    <h2 class="text-3xl font-extrabold text-gray-900">Cara Kerja Titip & Antar</h2>
                </div>
                <p class="text-gray-600 max-w-md text-sm md:text-right font-medium">
                    Alur pre-order transparan antar mahasiswa berbasis rute perjalanan pulang kampung.
                </p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="bg-white border border-[#EADBD1] p-6 rounded-2xl relative flex flex-col h-full">
                    <div class="flex justify-between items-start mb-6">
                        <div class="text-5xl font-black text-[#B5502E]">01</div>
                        <div class="p-2 bg-[#FDF8F5] text-gray-500 rounded-lg border border-[#EADBD1]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg mb-3 leading-snug">Jastiper Buat <br>Jadwal Rute</h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow leading-relaxed">
                        Mahasiswa yang pulang kampung mencantumkan kota asal (Solo, Purwokerto, Kudus, Tegal), kapasitas bagasi (kg), dan tanggal kembali ke UNNES.
                    </p>
                    <hr class="border-[#EADBD1] mb-4">
                    <div class="text-xs text-gray-500 font-medium">Input: Slot Bagasi & Estimasi Tiba</div>
                </div>

                <!-- Step 2 -->
                <div class="bg-white border border-[#EADBD1] p-6 rounded-2xl relative flex flex-col h-full">
                    <div class="flex justify-between items-start mb-6">
                        <div class="text-5xl font-black text-[#B5502E]">02</div>
                        <div class="p-2 bg-[#FDF8F5] text-gray-500 rounded-lg border border-[#EADBD1]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg mb-3 leading-snug">Buyer Ikut <br>Pre-Order</h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow leading-relaxed">
                        Mahasiswa perantau memilih makanan khas yang diinginkan sebelum batas waktu PO (cut-off) ditutup oleh jastiper bersangkutan.
                    </p>
                    <hr class="border-[#EADBD1] mb-4">
                    <div class="text-xs text-gray-500 font-medium">Batas: Waktu Cut-Off Rute</div>
                </div>

                <!-- Step 3 -->
                <div class="bg-white border border-[#EADBD1] p-6 rounded-2xl relative flex flex-col h-full">
                    <div class="flex justify-between items-start mb-6">
                        <div class="text-5xl font-black text-[#B5502E]">03</div>
                        <div class="p-2 bg-[#FDF8F5] text-gray-500 rounded-lg border border-[#EADBD1]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg mb-3 leading-snug">Bayar Aman via <br>Escrow QRIS</h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow leading-relaxed">
                        Dana pesanan disimpan aman di rekening bersama (escrow) dan baru diteruskan ke Jastiper setelah pesanan diterima utuh.
                    </p>
                    <hr class="border-[#EADBD1] mb-4">
                    <div class="text-xs text-gray-500 font-medium">Kliring: Terlindungi 100%</div>
                </div>

                <!-- Step 4 -->
                <div class="bg-white border border-[#EADBD1] p-6 rounded-2xl relative flex flex-col h-full">
                    <div class="flex justify-between items-start mb-6">
                        <div class="text-5xl font-black text-[#B5502E]">04</div>
                        <div class="p-2 bg-[#FDF8F5] text-gray-500 rounded-lg border border-[#EADBD1]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                        </div>
                    </div>
                    <h3 class="font-bold text-lg mb-3 leading-snug">Serah Terima di <br>Titik Kumpul UNNES</h3>
                    <p class="text-gray-600 text-sm mb-6 flex-grow leading-relaxed">
                        Ambil pesanan di titik temu yang disepakati (GSG UNNES, FTIK, FMIPA, atau Banaran) dengan verifikasi kode pesanan & KTM.
                    </p>
                    <hr class="border-[#EADBD1] mb-4">
                    <div class="text-xs text-gray-500 font-medium">Validasi: Verifikasi Kode & KTM</div>
                </div>
            </div>
        </div>
    </div>

    <!-- KATALOG PRE-ORDER SECTION -->
    <div class="py-16 md:py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:justify-between md:items-end gap-4 mb-8">
            <div>
                <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Katalog Pre-Order Aktif Minggu Ini</h2>
                <p class="text-gray-600 text-sm font-medium">Pesan sebelum jastiper kembali ke Semarang. Semua tujuan: Kampus UNNES Sekaran.</p>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-gray-500 font-medium bg-white px-3 py-1.5 rounded border border-[#EADBD1]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Update status otomatis setiap 30 menit
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex flex-wrap items-center gap-2 mb-8">
            <button class="bg-[#B5502E] text-white px-5 py-2 rounded-md text-sm font-bold shadow-sm">Semua</button>
            <button class="bg-[#F5EBE4] hover:bg-[#EADBD1] text-gray-700 border border-[#EADBD1] px-5 py-2 rounded-md text-sm font-semibold transition">Solo (Surakarta)</button>
            <button class="bg-[#F5EBE4] hover:bg-[#EADBD1] text-gray-700 border border-[#EADBD1] px-5 py-2 rounded-md text-sm font-semibold transition">Purwokerto (Banyumas)</button>
            <button class="bg-[#F5EBE4] hover:bg-[#EADBD1] text-gray-700 border border-[#EADBD1] px-5 py-2 rounded-md text-sm font-semibold transition">Kudus</button>
            <button class="bg-[#F5EBE4] hover:bg-[#EADBD1] text-gray-700 border border-[#EADBD1] px-5 py-2 rounded-md text-sm font-semibold transition">Tegal / Pekalongan</button>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="bg-white border border-[#EADBD1] rounded-xl p-5 hover:shadow-lg hover:border-[#B5502E] transition duration-300 flex flex-col h-full group">
                <div class="flex justify-between items-start mb-3">
                    <div class="text-xs font-extrabold text-gray-800">Solo &rarr; Sekaran</div>
                    <div class="text-[10px] font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 rounded">Sisa 4 slot</div>
                </div>
                <h3 class="font-extrabold text-lg leading-snug mb-2 group-hover:text-[#B5502E] transition">Serabi Notosuman & Abon Mesran Solo</h3>
                <div class="text-xs text-gray-500 mb-5 leading-relaxed">
                    Jastiper: <span class="font-bold text-gray-800">Rizky F.</span> (FT UNNES '22) &bull; <span class="text-[#B5502E] font-semibold">Terverifikasi KTM</span>
                </div>
                
                <div class="mb-5">
                    <div class="flex justify-between text-[11px] font-bold mb-1.5">
                        <span class="text-gray-600">Kuota Terisi</span>
                        <span class="text-gray-900">16/20 porsi</span>
                    </div>
                    <div class="w-full bg-[#F5EBE4] h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#B5502E] w-[80%] h-full rounded-full"></div>
                    </div>
                </div>
                
                <div class="space-y-2 mb-5 text-[11px] flex-grow">
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Countdown:</span>
                        <span class="font-bold text-[#B5502E]">PO Ditutup: 1 hari 8 jam</span>
                    </div>
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Jadwal Kembali:</span>
                        <span class="font-bold text-gray-800">Minggu, 20:00 WIB</span>
                    </div>
                    <div class="flex justify-between pb-1.5">
                        <span class="text-gray-500 font-medium">Titik Kumpul:</span>
                        <span class="font-bold text-gray-800">Depan GSG UNNES</span>
                    </div>
                </div>
                
                <div class="mt-auto pt-4 border-t border-[#EADBD1] mb-5">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-gray-500 text-[11px] font-medium">Estimasi Harga:</span>
                        <span class="font-extrabold text-lg text-gray-900">Rp 35.000 <span class="text-[10px] font-medium text-gray-500">/ porsi</span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-[11px] font-medium">Biaya Titip:</span>
                        <span class="font-bold text-[#B5502E] text-sm">Rp 5.000</span>
                    </div>
                </div>
                
                <a href="{{ route('katalog') }}" class="block w-full text-center border border-[#B5502E] text-[#B5502E] font-bold py-2.5 rounded-lg hover:bg-[#B5502E] hover:text-white transition text-sm">
                    Ikut PO Ini
                </a>
            </div>

            <!-- Card 2 -->
            <div class="bg-white border border-[#EADBD1] rounded-xl p-5 hover:shadow-lg hover:border-[#B5502E] transition duration-300 flex flex-col h-full group">
                <div class="flex justify-between items-start mb-3">
                    <div class="text-xs font-extrabold text-gray-800">Purwokerto &rarr; Sekaran</div>
                    <div class="text-[10px] font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 rounded">Sisa 2 slot</div>
                </div>
                <h3 class="font-extrabold text-lg leading-snug mb-2 group-hover:text-[#B5502E] transition">Mendoan Mentah Baturraden & Jenang Jaket Asli</h3>
                <div class="text-xs text-gray-500 mb-5 leading-relaxed">
                    Jastiper: <span class="font-bold text-gray-800">Annisa D.</span> (FBS UNNES '21) &bull; <span class="text-[#B5502E] font-semibold">Terverifikasi KTM</span>
                </div>
                
                <div class="mb-5">
                    <div class="flex justify-between text-[11px] font-bold mb-1.5">
                        <span class="text-gray-600">Kuota Terisi</span>
                        <span class="text-gray-900">13/15 porsi</span>
                    </div>
                    <div class="w-full bg-[#F5EBE4] h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#B5502E] w-[86%] h-full rounded-full"></div>
                    </div>
                </div>
                
                <div class="space-y-2 mb-5 text-[11px] flex-grow">
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Countdown:</span>
                        <span class="font-bold text-[#B5502E]">PO Ditutup: 9 jam lagi</span>
                    </div>
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Jadwal Kembali:</span>
                        <span class="font-bold text-gray-800">Sabtu, 17:30 WIB</span>
                    </div>
                    <div class="flex justify-between pb-1.5">
                        <span class="text-gray-500 font-medium">Titik Kumpul:</span>
                        <span class="font-bold text-gray-800">Simpang Banaran UNNES</span>
                    </div>
                </div>
                
                <div class="mt-auto pt-4 border-t border-[#EADBD1] mb-5">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-gray-500 text-[11px] font-medium">Estimasi Harga:</span>
                        <span class="font-extrabold text-lg text-gray-900">Rp 28.000 <span class="text-[10px] font-medium text-gray-500">/ paket</span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-[11px] font-medium">Biaya Titip:</span>
                        <span class="font-bold text-[#B5502E] text-sm">Rp 4.000</span>
                    </div>
                </div>
                
                <a href="{{ route('katalog') }}" class="block w-full text-center border border-[#B5502E] text-[#B5502E] font-bold py-2.5 rounded-lg hover:bg-[#B5502E] hover:text-white transition text-sm">
                    Ikut PO Ini
                </a>
            </div>

            <!-- Card 3 -->
            <div class="bg-white border border-[#EADBD1] rounded-xl p-5 hover:shadow-lg hover:border-[#B5502E] transition duration-300 flex flex-col h-full group">
                <div class="flex justify-between items-start mb-3">
                    <div class="text-xs font-extrabold text-gray-800">Kudus &rarr; Sekaran</div>
                    <div class="text-[10px] font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 rounded">Sisa 6 slot</div>
                </div>
                <h3 class="font-extrabold text-lg leading-snug mb-2 group-hover:text-[#B5502E] transition">Jenang Kudus Mubarok & Lentog Tanjung Box</h3>
                <div class="text-xs text-gray-500 mb-5 leading-relaxed">
                    Jastiper: <span class="font-bold text-gray-800">Bagas P.</span> (FEB UNNES '23) &bull; <span class="text-[#B5502E] font-semibold">Terverifikasi KTM</span>
                </div>
                
                <div class="mb-5">
                    <div class="flex justify-between text-[11px] font-bold mb-1.5">
                        <span class="text-gray-600">Kuota Terisi</span>
                        <span class="text-gray-900">14/20 kotak</span>
                    </div>
                    <div class="w-full bg-[#F5EBE4] h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#B5502E] w-[70%] h-full rounded-full"></div>
                    </div>
                </div>
                
                <div class="space-y-2 mb-5 text-[11px] flex-grow">
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Countdown:</span>
                        <span class="font-bold text-[#B5502E]">PO Ditutup: 2 hari lagi</span>
                    </div>
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Jadwal Kembali:</span>
                        <span class="font-bold text-gray-800">Senin, 10:00 WIB</span>
                    </div>
                    <div class="flex justify-between pb-1.5">
                        <span class="text-gray-500 font-medium">Titik Kumpul:</span>
                        <span class="font-bold text-gray-800">Lapangan Rektorat UNNES</span>
                    </div>
                </div>
                
                <div class="mt-auto pt-4 border-t border-[#EADBD1] mb-5">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-gray-500 text-[11px] font-medium">Estimasi Harga:</span>
                        <span class="font-extrabold text-lg text-gray-900">Rp 42.000 <span class="text-[10px] font-medium text-gray-500">/ kotak</span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-[11px] font-medium">Biaya Titip:</span>
                        <span class="font-bold text-[#B5502E] text-sm">Rp 5.000</span>
                    </div>
                </div>
                
                <a href="{{ route('katalog') }}" class="block w-full text-center border border-[#B5502E] text-[#B5502E] font-bold py-2.5 rounded-lg hover:bg-[#B5502E] hover:text-white transition text-sm">
                    Ikut PO Ini
                </a>
            </div>

            <!-- Card 4 -->
            <div class="bg-white border border-[#EADBD1] rounded-xl p-5 hover:shadow-lg hover:border-[#B5502E] transition duration-300 flex flex-col h-full group">
                <div class="flex justify-between items-start mb-3">
                    <div class="text-xs font-extrabold text-gray-800">Tegal &rarr; Sekaran</div>
                    <div class="text-[10px] font-bold text-[#B5502E] bg-[#FDF8F5] border border-[#EADBD1] px-2 py-0.5 rounded">Sisa 3 slot</div>
                </div>
                <h3 class="font-extrabold text-lg leading-snug mb-2 group-hover:text-[#B5502E] transition">Tahu Aci Khas Banjaran & Teh Poci Melati</h3>
                <div class="text-xs text-gray-500 mb-5 leading-relaxed">
                    Jastiper: <span class="font-bold text-gray-800">Dita W.</span> (FISIP UNNES '22) &bull; <span class="text-[#B5502E] font-semibold">Terverifikasi KTM</span>
                </div>
                
                <div class="mb-5">
                    <div class="flex justify-between text-[11px] font-bold mb-1.5">
                        <span class="text-gray-600">Kuota Terisi</span>
                        <span class="text-gray-900">12/15 besek</span>
                    </div>
                    <div class="w-full bg-[#F5EBE4] h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#B5502E] w-[80%] h-full rounded-full"></div>
                    </div>
                </div>
                
                <div class="space-y-2 mb-5 text-[11px] flex-grow">
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Countdown:</span>
                        <span class="font-bold text-[#B5502E]">PO Ditutup: 14 jam lagi</span>
                    </div>
                    <div class="flex justify-between border-b border-[#F5EBE4] pb-1.5">
                        <span class="text-gray-500 font-medium">Jadwal Kembali:</span>
                        <span class="font-bold text-gray-800">Minggu, 15:00 WIB</span>
                    </div>
                    <div class="flex justify-between pb-1.5">
                        <span class="text-gray-500 font-medium">Titik Kumpul:</span>
                        <span class="font-bold text-gray-800">Bundaran Konservasi UNNES</span>
                    </div>
                </div>
                
                <div class="mt-auto pt-4 border-t border-[#EADBD1] mb-5">
                    <div class="flex justify-between items-end mb-1">
                        <span class="text-gray-500 text-[11px] font-medium">Estimasi Harga:</span>
                        <span class="font-extrabold text-lg text-gray-900">Rp 25.000 <span class="text-[10px] font-medium text-gray-500">/ besek</span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500 text-[11px] font-medium">Biaya Titip:</span>
                        <span class="font-bold text-[#B5502E] text-sm">Rp 3.500</span>
                    </div>
                </div>
                
                <a href="{{ route('katalog') }}" class="block w-full text-center border border-[#B5502E] text-[#B5502E] font-bold py-2.5 rounded-lg hover:bg-[#B5502E] hover:text-white transition text-sm">
                    Ikut PO Ini
                </a>
            </div>
        </div>

        <div class="mt-12 flex justify-center">
            <button class="bg-[#FDF8F5] border border-[#EADBD1] text-[#B5502E] font-bold px-8 py-3 rounded-lg hover:bg-[#F5EBE4] transition flex items-center gap-2">
                Lihat Semua 28 Jadwal Rute Jastip
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </div>
        
        <hr class="border-[#EADBD1] mt-16">
    </div>

    <!-- KEAMANAN & TRANSPARANSI SECTION -->
    <div class="pb-16 md:pb-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <div class="text-[#B5502E] text-[10px] font-bold tracking-widest uppercase mb-3">KEAMANAN & TRANSPARANSI</div>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Sistem Transaksi Khusus Mahasiswa</h2>
            <p class="text-gray-600 max-w-2xl text-sm font-medium leading-relaxed">
                Dirancang untuk memastikan kenyamanan titip-menitip tanpa risiko pembatalan sepihak atau barang tidak sesuai.
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Feature 1 -->
            <div class="bg-white border border-[#EADBD1] p-8 rounded-2xl flex flex-col h-full">
                <div class="w-12 h-12 bg-[#FDF8F5] border border-[#EADBD1] rounded-xl flex items-center justify-center text-[#B5502E] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                </div>
                <h3 class="font-bold text-lg mb-3">Proteksi Dana Escrow Otomatis</h3>
                <p class="text-gray-600 text-sm mb-6 flex-grow leading-relaxed">
                    Pembayaran melalui QRIS terverifikasi ditahan dalam rekening penampung. Dana hanya dicairkan kepada jastiper setelah pembeli memeriksa barang di titik kumpul.
                </p>
                <div class="text-xs font-bold text-gray-800 border-t border-[#EADBD1] pt-4">Klaim Refund Instan Jika Rute Batal</div>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white border border-[#EADBD1] p-8 rounded-2xl flex flex-col h-full">
                <div class="w-12 h-12 bg-[#FDF8F5] border border-[#EADBD1] rounded-xl flex items-center justify-center text-[#B5502E] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                </div>
                <h3 class="font-bold text-lg mb-3">Verifikasi Identitas Mahasiswa (KTM/SSO)</h3>
                <p class="text-gray-600 text-sm mb-6 flex-grow leading-relaxed">
                    Setiap Jastiper dan Pembeli wajib memvalidasi akun menggunakan email resmi institusi atau Kartu Tanda Mahasiswa UNNES yang aktif.
                </p>
                <div class="text-xs font-bold text-gray-800 border-t border-[#EADBD1] pt-4">Tervalidasi Database Mahasiswa Aktif</div>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white border border-[#EADBD1] p-8 rounded-2xl flex flex-col h-full">
                <div class="w-12 h-12 bg-[#FDF8F5] border border-[#EADBD1] rounded-xl flex items-center justify-center text-[#B5502E] mb-6">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                </div>
                <h3 class="font-bold text-lg mb-3">Titik Serah Terima Terpantau</h3>
                <p class="text-gray-600 text-sm mb-6 flex-grow leading-relaxed">
                    Pengambilan barang difokuskan di area publik kampus UNNES (Sekaran, GSG, Bundaran Konservasi) demi keamanan bersama.
                </p>
                <div class="text-xs font-bold text-gray-800 border-t border-[#EADBD1] pt-4">Koordinasi Terbuka di Titik Kumpul</div>
            </div>
        </div>
    </div>

    <!-- FOOTER BANNER KOLABORASI -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-16">
        <div class="bg-[#F5EBE4] border border-[#EADBD1] rounded-2xl p-8 lg:p-10 flex flex-col lg:flex-row justify-between items-start lg:items-center gap-8">
            <div class="max-w-xl">
                <div class="flex items-center gap-2 text-[#B5502E] text-[10px] font-bold tracking-widest uppercase mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    KOLABORASI KOMUNITAS KAMPUS
                </div>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-4 leading-snug">
                    Bekerja sama dengan Paguyuban Daerah Mahasiswa di UNNES untuk verifikasi kuliner autentik dan pengawasan jastiper terpercaya.
                </h2>
                <p class="text-sm text-gray-600 font-medium leading-relaxed">
                    Setiap rute dikurasi bersama perwakilan paguyuban guna memastikan keaslian produsen oleh-oleh lokal di kota asal serta menjaga komitmen jadwal kembali ke Semarang.
                </p>
            </div>
            
            <div class="w-full lg:w-auto shrink-0">
                <div class="text-[10px] font-bold text-gray-800 tracking-widest uppercase mb-4">MITRA PAGUYUBAN MAHASISWA DAERAH:</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="bg-white px-5 py-3 border border-[#EADBD1] rounded-lg flex items-center gap-2 text-sm font-bold text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#B5502E]"></span> Pakemas (Banyumas)
                    </div>
                    <div class="bg-white px-5 py-3 border border-[#EADBD1] rounded-lg flex items-center gap-2 text-sm font-bold text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#B5502E]"></span> FKM-SR (Solo Raya)
                    </div>
                    <div class="bg-white px-5 py-3 border border-[#EADBD1] rounded-lg flex items-center gap-2 text-sm font-bold text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#B5502E]"></span> IMK (Kudus)
                    </div>
                    <div class="bg-white px-5 py-3 border border-[#EADBD1] rounded-lg flex items-center gap-2 text-sm font-bold text-gray-700">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#B5502E]"></span> IPMT (Tegal)
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
