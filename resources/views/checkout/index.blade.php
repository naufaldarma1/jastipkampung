<x-app-layout>
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#B5502E]">Langkah 2 · Checkout aman</p>
            <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Konfirmasi & Pembayaran Pre-Order</h1>
            <div class="mt-3 flex flex-wrap gap-2 text-xs font-bold text-gray-700">
                <span class="rounded-full bg-[#F5EBE4] px-3 py-1.5">{{ $itemCount }} item dalam pesanan</span>
                <span class="rounded-full bg-[#F5EBE4] px-3 py-1.5">Pilih 1 titik temu resmi di UNNES</span>
            </div>
        </div>

        @if ($errors->any())
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('checkout.store') }}" class="space-y-5">
            @csrf
            <section class="rounded-2xl border border-[#EADBD1] bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3 border-b border-[#F5EBE4] pb-4">
                    <div>
                        <h2 class="font-extrabold text-gray-900">1. Pilih Titik Kumpul di UNNES</h2>
                        <p class="mt-1 text-xs text-gray-600">Pilih satu titik temu resmi di area UNNES untuk pengambilan pesananmu.</p>
                    </div>
                    <span class="rounded bg-[#FDF8F5] px-2 py-1 text-[10px] font-bold uppercase text-[#B5502E]">Titik resmi</span>
                </div>
                <div class="space-y-3">
                    @foreach ($pickupPointOptions as $point)
                        <label class="flex cursor-pointer items-start gap-3 rounded-xl border border-[#EADBD1] p-4 hover:border-[#B5502E] has-[:checked]:border-[#B5502E] has-[:checked]:bg-[#FDF8F5]">
                            <input type="radio" name="pickup_point" value="{{ $point['name'] }}" required @checked($selectedPickupPoint === $point['name']) class="mt-1 border-[#DCC4B5] text-[#B5502E] focus:ring-[#B5502E]">
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-extrabold text-gray-900">{{ $point['name'] }}</span>
                                <span class="mt-1 block text-xs text-gray-600">{{ $point['description'] }}</span>
                            </span>
                            @if (! empty($point['recommended']))
                                <span class="shrink-0 rounded-full bg-[#B5502E] px-2.5 py-1 text-[10px] font-bold text-white">Rekomendasi jastiper</span>
                            @else
                                <span class="shrink-0 rounded-full bg-[#F5EBE4] px-2.5 py-1 text-[10px] font-bold text-[#B5502E]">{{ $point['badge'] ?? 'Titik resmi' }}</span>
                            @endif
                        </label>
                    @endforeach
                </div>
            </section>

            <section class="rounded-2xl border border-[#EADBD1] bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4 border-b border-[#F5EBE4] pb-4">
                    <h2 class="font-extrabold text-gray-900">2. Ringkasan Pesanan ({{ $itemCount }} item)</h2>
                    <p class="mt-1 text-xs text-gray-600">Periksa titipan dan biaya sebelum lanjut.</p>
                </div>
                <div class="space-y-3">
                    @foreach ($items as $item)
                        <div class="flex items-start justify-between gap-4 rounded-xl bg-[#FDF8F5] p-4">
                            <div>
                                <p class="font-extrabold text-gray-900">{{ $item['title'] }}</p>
                                <p class="mt-1 text-xs text-gray-600">{{ $item['quantity'] }} {{ $item['unit'] }} · {{ str_replace('->', '→', $item['route']) }}</p>
                                <p class="mt-1 text-[11px] text-gray-500">Rekomendasi titik jastiper: {{ $item['pickup_point'] }} · mengikuti titik pilihan di Langkah 1</p>
                            </div>
                            <p class="shrink-0 text-sm font-extrabold text-gray-900">Rp {{ number_format($item['total_price'], 0, ',', '.') }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 space-y-2 border-t border-[#F5EBE4] pt-4 text-sm">
                    <div class="flex justify-between"><span class="text-gray-600">Total titipan termasuk biaya layanan</span><span class="font-extrabold text-[#B5502E]">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                    <p class="text-xs text-gray-500">Pembayaran dilakukan langsung sesuai instruksi jastiper. Situs ini belum memproses dana secara otomatis.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-[#EADBD1] bg-white p-5 shadow-sm sm:p-6">
                <div class="mb-4 border-b border-[#F5EBE4] pb-4">
                    <h2 class="font-extrabold text-gray-900">3. Metode Pembayaran</h2>
                    <p class="mt-1 text-xs text-gray-600">Pilih cara pembayaran untuk seluruh pesanan ini.</p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <label class="cursor-pointer rounded-xl border border-[#EADBD1] p-4 hover:border-[#B5502E] has-[:checked]:border-[#B5502E] has-[:checked]:bg-[#FDF8F5]">
                        <span class="flex items-start gap-3"><input type="radio" name="payment_method" value="qris" required {{ old('payment_method') === 'qris' ? 'checked' : '' }} class="mt-1 border-[#DCC4B5] text-[#B5502E] focus:ring-[#B5502E]"><span><span class="block font-extrabold text-gray-900">QRIS</span><span class="mt-1 block text-xs text-gray-600">Instruksi/QR dari jastiper setelah konfirmasi.</span></span></span>
                    </label>
                    <label class="cursor-pointer rounded-xl border border-[#EADBD1] p-4 hover:border-[#B5502E] has-[:checked]:border-[#B5502E] has-[:checked]:bg-[#FDF8F5]">
                        <span class="flex items-start gap-3"><input type="radio" name="payment_method" value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'checked' : '' }} class="mt-1 border-[#DCC4B5] text-[#B5502E] focus:ring-[#B5502E]"><span><span class="block font-extrabold text-gray-900">Transfer Bank</span><span class="mt-1 block text-xs text-gray-600">Nomor rekening diberikan jastiper.</span></span></span>
                    </label>
                    <label class="cursor-pointer rounded-xl border border-[#EADBD1] p-4 hover:border-[#B5502E] has-[:checked]:border-[#B5502E] has-[:checked]:bg-[#FDF8F5]">
                        <span class="flex items-start gap-3"><input type="radio" name="payment_method" value="cod" {{ old('payment_method') === 'cod' ? 'checked' : '' }} class="mt-1 border-[#DCC4B5] text-[#B5502E] focus:ring-[#B5502E]"><span><span class="block font-extrabold text-gray-900">COD</span><span class="mt-1 block text-xs text-gray-600">Bayar langsung saat mengambil titipan.</span></span></span>
                    </label>
                </div>
            </section>

            <div class="sticky bottom-0 rounded-2xl border border-[#EADBD1] bg-white p-5 shadow-lg sm:flex sm:items-center sm:justify-between sm:p-6">
                <div><p class="text-xs font-semibold text-gray-500">TOTAL PEMBAYARAN</p><p class="mt-1 text-2xl font-extrabold text-[#B5502E]">Rp {{ number_format($total, 0, ',', '.') }}</p></div>
                <button class="mt-4 inline-flex w-full items-center justify-center gap-2 rounded-lg bg-[#B5502E] px-6 py-3 text-sm font-bold text-white hover:bg-[#9E4527] sm:mt-0 sm:w-auto">Lanjutkan Pembayaran <span aria-hidden="true">→</span></button>
            </div>
        </form>
    </div>
</x-app-layout>
