<x-app-layout>
    @php
        $paymentStatus = $orders->first()->payment_status;
        $isCod = $orders->first()->payment_method === 'cod';
        $paymentLabels = ['submitted' => 'Konfirmasi terkirim', 'due_on_pickup' => 'Bayar saat pengambilan', 'paid' => 'Pembayaran diterima', 'unpaid' => 'Menunggu pembayaran'];
    @endphp
    <div class="mx-auto max-w-3xl px-4 py-12 sm:px-6 lg:px-8">
        <section class="rounded-3xl border border-[#EADBD1] bg-white p-6 text-center shadow-sm sm:p-10">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                <svg class="h-8 w-8" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" /></svg>
            </div>
            <span class="mt-5 inline-flex rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide text-emerald-800">{{ $paymentLabels[$paymentStatus] ?? ucfirst($paymentStatus) }}</span>
            <h1 class="mt-3 text-3xl font-extrabold text-gray-900">{{ $isCod ? 'Pesanan COD dicatat!' : 'Konfirmasi pembayaran terkirim' }}</h1>
            <p class="mx-auto mt-3 max-w-lg text-sm leading-relaxed text-gray-600">
                @if ($isCod)
                    Pesananmu tercatat. Siapkan pembayaran saat mengambil titipan; status pesanan dapat dipantau dari dashboard.
                @else
                    Jastiper perlu memeriksa pembayaranmu. Status ini belum berarti pembayaran sudah diverifikasi.
                @endif
            </p>

            <div class="mx-auto mt-7 max-w-lg rounded-2xl bg-[#FDF8F5] p-5 text-left">
                <div class="flex justify-between gap-4 text-sm"><span class="text-gray-600">Referensi</span><span class="font-extrabold text-gray-900">{{ strtoupper(substr($reference, 0, 8)) }}</span></div>
                <div class="mt-3 flex justify-between gap-4 text-sm"><span class="text-gray-600">Total pesanan</span><span class="font-extrabold text-[#B5502E]">Rp {{ number_format($total, 0, ',', '.') }}</span></div>
                @foreach ($orders as $order)
                    <div class="mt-3 border-t border-[#EADBD1] pt-3 text-sm">
                        <p class="font-bold text-gray-900">{{ $order->product_title }} · {{ $order->quantity }} item</p>
                        <p class="mt-1 text-xs text-gray-600">Titik pengambilan: {{ $order->pickup_point }}</p>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('pesanan.index') }}" class="mt-7 inline-flex w-full max-w-lg items-center justify-center gap-2 rounded-xl bg-[#B5502E] px-5 py-3.5 text-sm font-extrabold text-white hover:bg-[#9E4527]">Lihat Dashboard Pesanan <span aria-hidden="true">→</span></a>
        </section>
    </div>
</x-app-layout>
