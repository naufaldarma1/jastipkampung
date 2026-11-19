<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="mb-8">
            <a href="{{ route('dashboard.jastiper') }}" class="text-sm font-bold text-[#B5502E] hover:underline">← Kembali ke Dashboard Jastiper</a>
            <p class="mt-6 text-xs font-bold uppercase tracking-[0.2em] text-[#B5502E]">Penawaran baru</p>
            <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Buat PO Perjalanan</h1>
            <p class="mt-2 text-sm text-gray-600">Isi detail daerah tujuan dan titipan. Setelah diterbitkan, PO tampil di katalog untuk pembeli.</p>
        </div>

        <form method="POST" action="{{ route('jastiper.trips.store') }}" class="space-y-6 rounded-2xl border border-[#EADBD1] bg-white p-6 shadow-sm sm:p-8">
            @csrf
            <div>
                <h2 class="text-lg font-extrabold text-gray-900">Info perjalanan dan titipan</h2>
                <p class="mt-1 text-sm text-gray-500">Satu PO mewakili satu penawaran produk/rute. Kamu bisa membuat PO lain untuk produk atau perjalanan berbeda.</p>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="title" class="mb-1.5 block text-sm font-bold text-gray-700">Nama produk / judul PO</label>
                    <input id="title" name="title" value="{{ old('title') }}" required maxlength="120" placeholder="Contoh: Mendoan asli Purwokerto" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                    @error('title')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="origin" class="mb-1.5 block text-sm font-bold text-gray-700">Daerah asal / tujuan perjalanan</label>
                    <input id="origin" name="origin" value="{{ old('origin') }}" required maxlength="80" placeholder="Contoh: Purwokerto" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                    @error('origin')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="destination" class="mb-1.5 block text-sm font-bold text-gray-700">Tujuan kembali</label>
                    <input id="destination" name="destination" value="{{ old('destination', 'Sekaran, UNNES') }}" required maxlength="80" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                    @error('destination')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="description" class="mb-1.5 block text-sm font-bold text-gray-700">Catatan untuk calon penitip <span class="font-medium text-gray-500">(opsional)</span></label>
                    <textarea id="description" name="description" rows="3" maxlength="1000" placeholder="Varian, cara pemesanan, atau info tambahan" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="border-t border-[#F5EBE4] pt-6">
                <h2 class="text-lg font-extrabold text-gray-900">Kuota dan biaya</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="quota" class="mb-1.5 block text-sm font-bold text-gray-700">Kuota tersedia</label>
                        <input id="quota" name="quota" type="number" min="1" max="1000" value="{{ old('quota') }}" required class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                        @error('quota')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="unit" class="mb-1.5 block text-sm font-bold text-gray-700">Satuan</label>
                        <input id="unit" name="unit" value="{{ old('unit', 'paket') }}" required maxlength="30" placeholder="paket, kotak, porsi" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                        @error('unit')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="price" class="mb-1.5 block text-sm font-bold text-gray-700">Harga produk per satuan (Rp)</label>
                        <input id="price" name="price" type="number" min="0" step="1000" value="{{ old('price') }}" required class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                        @error('price')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="service_fee" class="mb-1.5 block text-sm font-bold text-gray-700">Biaya titip per satuan (Rp)</label>
                        <input id="service_fee" name="service_fee" type="number" min="0" step="500" value="{{ old('service_fee', 0) }}" required class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                        @error('service_fee')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="border-t border-[#F5EBE4] pt-6">
                <h2 class="text-lg font-extrabold text-gray-900">Jadwal dan serah terima</h2>
                <div class="mt-4 grid gap-5 sm:grid-cols-2">
                    <div>
                        <label for="order_deadline" class="mb-1.5 block text-sm font-bold text-gray-700">Batas akhir pemesanan</label>
                        <input id="order_deadline" name="order_deadline" type="datetime-local" value="{{ old('order_deadline') }}" required class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                        @error('order_deadline')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="return_at" class="mb-1.5 block text-sm font-bold text-gray-700">Jadwal kembali ke kampus</label>
                        <input id="return_at" name="return_at" type="datetime-local" value="{{ old('return_at') }}" required class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                        @error('return_at')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="pickup_point" class="mb-1.5 block text-sm font-bold text-gray-700">Titik temu pengambilan di kampus</label>
                        <input id="pickup_point" name="pickup_point" value="{{ old('pickup_point') }}" required maxlength="150" placeholder="Contoh: Depan GSG UNNES" class="w-full rounded-lg border-[#EADBD1] focus:border-[#B5502E] focus:ring-[#B5502E]/20">
                        @error('pickup_point')<p class="mt-1 text-sm font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <div class="flex flex-col-reverse gap-3 border-t border-[#F5EBE4] pt-6 sm:flex-row sm:justify-end">
                <a href="{{ route('dashboard.jastiper') }}" class="rounded-lg border border-[#EADBD1] px-5 py-3 text-center text-sm font-bold text-gray-700 hover:bg-[#FDF8F5]">Batal</a>
                <button type="submit" class="rounded-lg bg-[#B5502E] px-6 py-3 text-sm font-bold text-white hover:bg-[#9E4527]">Terbitkan PO</button>
            </div>
        </form>
    </div>
</x-app-layout>
