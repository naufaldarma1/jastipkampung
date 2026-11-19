<x-app-layout>
    @php
        $methodLabels = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'cod' => 'COD'];
        $paymentStates = ['unpaid' => 'Menunggu pembayaran', 'submitted' => 'Menunggu verifikasi jastiper', 'due_on_pickup' => 'Dibayar saat pengambilan', 'paid' => 'Pembayaran diterima'];
        $paymentState = $orders->first()->payment_status;
    @endphp
    <div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-7">
            <span class="rounded-full bg-[#F5EBE4] px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wider text-[#B5502E]">Langkah 2 dari 2 · Pembayaran aman</span>
            <h1 class="mt-4 text-3xl font-extrabold text-gray-900">Selesaikan pembayaran pesanan Anda</h1>
            <p class="mt-2 max-w-2xl text-sm leading-relaxed text-gray-600">Referensi pesanan <span class="font-bold text-gray-800">{{ strtoupper(substr($reference, 0, 8)) }}</span>. Ikuti instruksi pembayaran jastiper untuk menyelesaikan pre-order.</p>
        </div>

        <section class="rounded-2xl border-2 border-[#B5502E] bg-white p-5 shadow-sm sm:p-8">
            <div class="text-center">
                <span class="inline-flex rounded-full bg-[#FDF8F5] px-3 py-1.5 text-[10px] font-extrabold uppercase tracking-wide text-[#B5502E]">{{ $paymentStates[$paymentState] ?? ucfirst($paymentState) }}</span>
                <h2 class="mt-3 text-lg font-extrabold text-gray-900">Bayar dengan {{ $methodLabels[$paymentMethod] ?? $paymentMethod }}</h2>
                @if ($paymentMethod === 'cod')
                    <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-gray-600">Kamu memilih COD. Siapkan pembayaran dan bayar kepada jastiper saat mengambil titipan di titik temu.</p>
                @elseif ($paymentMethod === 'qris')
                    <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-gray-600">Minta kode QRIS resmi kepada jastiper setelah pesanan dikonfirmasi. Pastikan nama penerima dan jumlah pembayaran sesuai sebelum memindai.</p>
                @else
                    <p class="mx-auto mt-2 max-w-md text-xs leading-relaxed text-gray-600">Minta nomor rekening resmi kepada jastiper setelah pesanan dikonfirmasi. Pastikan nama penerima dan jumlah pembayaran sesuai sebelum transfer.</p>
                @endif
            </div>

            @if ($paymentMethod !== 'cod')
                <div class="mx-auto mt-6 max-w-sm rounded-2xl border border-dashed border-[#DCC4B5] bg-[#FDF8F5] p-6 text-center">
                    <div class="mx-auto flex h-36 w-36 flex-col items-center justify-center rounded-xl border border-[#EADBD1] bg-white p-4 text-[#B5502E]">
                        <span class="text-3xl font-black tracking-tight">{{ $paymentMethod === 'qris' ? 'QRIS' : 'BANK' }}</span>
                        <span class="mt-2 text-center text-[10px] font-bold uppercase tracking-wide text-gray-500">Instruksi dari jastiper</span>
                    </div>
                    <p class="mt-4 text-sm font-extrabold text-gray-900">{{ $paymentMethod === 'qris' ? 'QRIS dari jastiper' : 'Rekening bank dari jastiper' }}</p>
                    <p class="mt-1 text-xs leading-relaxed text-gray-600">Detail pembayaran belum tersedia di sistem. Hubungi jastiper melalui kanal yang disepakati; jangan membayar ke kode atau rekening yang tidak terverifikasi.</p>
                </div>
            @endif

            <div class="mt-7 text-center">
                <p class="text-xs font-bold uppercase tracking-wide text-gray-500">Total pembayaran</p>
                <p class="mt-1 text-4xl font-extrabold text-[#B5502E]">Rp {{ number_format($total, 0, ',', '.') }}</p>
                <p class="mt-2 text-xs text-gray-500">{{ $orders->count() }} pesanan · pembayaran dicatat setelah dikonfirmasi jastiper</p>
            </div>

            @if (! in_array($paymentState, ['submitted', 'paid'], true))
                <form method="POST" action="{{ route('payment.confirm', $reference) }}" class="mt-7">
                    @csrf
                    <button class="w-full rounded-xl bg-[#B5502E] px-5 py-3.5 text-sm font-extrabold text-white shadow-sm hover:bg-[#9E4527]">
                        {{ $paymentMethod === 'cod' ? 'Konfirmasi Pesanan COD' : 'Saya Sudah Membayar — Kirim Konfirmasi' }}
                    </button>
                </form>
            @else
                <a href="{{ route('payment.receipt', $reference) }}" class="mt-7 block w-full rounded-xl border border-[#EADBD1] px-5 py-3.5 text-center text-sm font-extrabold text-gray-800 hover:bg-[#FDF8F5]">Lihat Ringkasan Konfirmasi</a>
            @endif
        </section>

        <div class="mt-5 rounded-2xl border border-[#EADBD1] bg-[#FDF8F5] p-4 text-sm">
            <p class="font-extrabold text-gray-900">Status pembayaran</p>
            <p class="mt-1 text-gray-600">{{ $paymentStates[$paymentState] ?? ucfirst($paymentState) }}. Konfirmasi pembeli tidak berarti pembayaran sudah diverifikasi; jastiper perlu mengecek pembayaran secara langsung.</p>
        </div>
    </div>
</x-app-layout>
