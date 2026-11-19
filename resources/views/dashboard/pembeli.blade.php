<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8 rounded-2xl bg-[#B5502E] p-8 text-white shadow-sm">
            <p class="mb-2 text-sm font-bold uppercase tracking-widest text-white/80">Dashboard Pembeli</p>
            <h1 class="text-3xl font-extrabold">Halo, {{ Auth::user()->name }}!</h1>
            <p class="mt-2 max-w-2xl text-sm text-white/90">Cari makanan khas kampung halaman, pesan sesuai kuota, lalu pantau statusnya di satu tempat.</p>
            <a href="{{ route('katalog') }}" class="mt-6 inline-flex rounded-lg bg-white px-5 py-3 text-sm font-bold text-[#B5502E] hover:bg-[#FDF8F5]">Jelajahi Katalog PO</a>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <a href="{{ route('katalog') }}" class="rounded-2xl border border-[#EADBD1] bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-sm font-bold text-[#B5502E]">KATALOG</p>
                <h2 class="mt-2 text-xl font-extrabold text-gray-900">Temukan titipan berikutnya</h2>
                <p class="mt-2 text-sm text-gray-600">Cari berdasarkan makanan atau kota asal, lalu lihat kuota dan titik pengambilan.</p>
            </a>
            <a href="{{ route('pesanan.index') }}" class="rounded-2xl border border-[#EADBD1] bg-white p-6 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                <p class="text-sm font-bold text-[#B5502E]">PESANAN SAYA</p>
                <h2 class="mt-2 text-xl font-extrabold text-gray-900">Lacak dan kelola pesanan</h2>
                <p class="mt-2 text-sm text-gray-600">Periksa total biaya, jadwal rute, titik temu, dan batalkan pesanan yang belum dikonfirmasi.</p>
            </a>
        </div>
    </div>
</x-app-layout>
