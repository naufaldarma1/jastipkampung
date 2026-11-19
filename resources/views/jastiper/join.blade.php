<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-[#EADBD1] bg-white shadow-sm">
            <div class="bg-[#B5502E] px-6 py-8 text-white sm:px-10">
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-white/80">Gabung sebagai Mitra</p>
                <h1 class="mt-2 text-3xl font-extrabold">Jadi Jastiper Kampung</h1>
                <p class="mt-3 max-w-2xl text-sm leading-relaxed text-white/90">Bantu teman kampus mendapatkan kuliner khas dari kota asalmu. Setelah bergabung, akunmu akan mendapat akses ke dashboard Jastiper.</p>
            </div>

            <div class="grid gap-8 p-6 sm:p-10 md:grid-cols-2">
                <div>
                    <h2 class="text-lg font-extrabold text-gray-900">Sebelum mulai</h2>
                    <ul class="mt-4 space-y-3 text-sm leading-relaxed text-gray-600">
                        <li class="flex gap-3"><span class="font-bold text-[#B5502E]">01</span><span>Gunakan nama dan kontak yang mudah dikenali teman kampus.</span></li>
                        <li class="flex gap-3"><span class="font-bold text-[#B5502E]">02</span><span>Pastikan jadwal perjalanan dan kapasitas titipan jelas.</span></li>
                        <li class="flex gap-3"><span class="font-bold text-[#B5502E]">03</span><span>Sepakati biaya dan titik serah terima sebelum menerima pesanan.</span></li>
                    </ul>
                </div>

                <div class="rounded-xl border border-[#EADBD1] bg-[#FDF8F5] p-5">
                    <h2 class="font-extrabold text-gray-900">Siap bergabung?</h2>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600">Peran akunmu akan diubah menjadi Jastiper. Kamu tetap bisa melihat katalog dan mengelola profil.</p>
                    <form method="POST" action="{{ route('jastiper.join.store') }}" class="mt-5">
                        @csrf
                        <button type="submit" class="w-full rounded-lg bg-[#B5502E] px-5 py-3 text-sm font-bold text-white transition hover:bg-[#9E4527]">Aktifkan Akun Jastiper</button>
                    </form>
                    <a href="{{ route('dashboard.pembeli') }}" class="mt-4 block text-center text-sm font-bold text-gray-600 hover:text-gray-900">Kembali ke dashboard</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
