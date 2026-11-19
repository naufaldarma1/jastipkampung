<x-app-layout>
    <div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#B5502E]">Langkah 1 · Keranjang</p>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Keranjang Pesanan</h1>
                <p class="mt-2 text-sm text-gray-600">Periksa jumlah titipan sebelum memilih metode pembayaran.</p>
            </div>
            <a href="{{ route('katalog') }}" class="text-sm font-bold text-[#B5502E] hover:underline">+ Tambah titipan lain</a>
        </div>

        @if (session('status'))
            <div role="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
        @endif

        @forelse ($items as $item)
            <article class="mb-4 rounded-2xl border border-[#EADBD1] bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="text-xs font-bold uppercase tracking-wide text-[#B5502E]">{{ $item['route'] }}</p>
                        <h2 class="mt-1 text-lg font-extrabold text-gray-900">{{ $item['title'] }}</h2>
                        <p class="mt-1 text-sm text-gray-600">Titik temu: {{ $item['pickup_point'] }}</p>
                        <p class="mt-2 text-sm font-bold text-gray-800">Rp {{ number_format($item['unit_price'], 0, ',', '.') }} + biaya titip Rp {{ number_format($item['service_fee'], 0, ',', '.') }} / {{ $item['unit'] }}</p>
                    </div>
                    <div class="flex items-center justify-between gap-4 sm:justify-end">
                        <form method="POST" action="{{ route('cart.update', $item['key']) }}" class="flex items-center gap-2">
                            @csrf
                            @method('PATCH')
                            <label class="sr-only" for="quantity-{{ $item['key'] }}">Jumlah</label>
                            <input id="quantity-{{ $item['key'] }}" type="number" name="quantity" min="1" max="{{ max(1, $item['available']) }}" value="{{ $item['quantity'] }}" class="w-20 rounded-lg border border-[#EADBD1] text-sm focus:border-[#B5502E] focus:ring-[#B5502E]">
                            <button class="rounded-lg border border-[#EADBD1] px-3 py-2 text-xs font-bold text-gray-700 hover:bg-[#FDF8F5]">Ubah</button>
                        </form>
                        <form method="POST" action="{{ route('cart.remove', $item['key']) }}">
                            @csrf
                            @method('DELETE')
                            <button class="text-xs font-bold text-red-600 hover:underline">Hapus</button>
                        </form>
                    </div>
                </div>
                <div class="mt-4 border-t border-[#F5EBE4] pt-4 text-right">
                    <span class="text-xs font-semibold text-gray-500">Subtotal</span>
                    <p class="text-lg font-extrabold text-[#B5502E]">Rp {{ number_format($item['total_price'], 0, ',', '.') }}</p>
                </div>
            </article>
        @empty
            <div class="rounded-2xl border border-dashed border-[#DCC4B5] bg-white px-6 py-16 text-center">
                <h2 class="text-xl font-extrabold text-gray-900">Keranjang masih kosong</h2>
                <p class="mx-auto mt-2 max-w-md text-sm text-gray-600">Pilih titipan dari katalog, lalu tambahkan ke keranjang.</p>
                <a href="{{ route('katalog') }}" class="mt-6 inline-flex rounded-lg bg-[#B5502E] px-5 py-3 text-sm font-bold text-white hover:bg-[#9E4527]">Lihat Katalog</a>
            </div>
        @endforelse

        @if ($items->isNotEmpty())
            <div class="sticky bottom-0 mt-6 rounded-2xl border border-[#EADBD1] bg-white p-5 shadow-lg sm:flex sm:items-center sm:justify-between sm:p-6">
                <div>
                    <p class="text-sm font-semibold text-gray-500">{{ $itemCount }} item · Estimasi total</p>
                    <p class="mt-1 text-2xl font-extrabold text-[#B5502E]">Rp {{ number_format($total, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('checkout.index') }}" class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#B5502E] px-6 py-3 text-sm font-bold text-white hover:bg-[#9E4527] sm:mt-0 sm:w-auto">Lanjut Checkout <span aria-hidden="true">→</span></a>
            </div>
        @endif
    </div>
</x-app-layout>
