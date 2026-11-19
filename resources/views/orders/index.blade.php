<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
        @php
            $filters = [
                'semua' => 'Semua',
                'perjalanan' => 'Dalam Perjalanan',
                'diproses' => 'Sedang Diproses',
                'selesai' => 'Selesai',
                'dibatalkan' => 'Dibatalkan',
            ];
            $statusLabels = [
                'pending' => 'Menunggu konfirmasi',
                'confirmed' => 'PO dikonfirmasi',
                'purchased' => 'Titipan sudah dibeli',
                'in_transit' => 'Dalam perjalanan',
                'ready_for_pickup' => 'Siap diambil',
                'completed' => 'Selesai · serah terima',
                'cancelled' => 'Dibatalkan',
            ];
            $statusClasses = [
                'pending' => 'bg-amber-100 text-amber-900',
                'confirmed' => 'bg-blue-100 text-blue-900',
                'purchased' => 'bg-indigo-100 text-indigo-900',
                'in_transit' => 'bg-orange-100 text-orange-900',
                'ready_for_pickup' => 'bg-teal-100 text-teal-900',
                'completed' => 'bg-emerald-100 text-emerald-900',
                'cancelled' => 'bg-gray-100 text-gray-700',
            ];
            $paymentLabels = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer Bank', 'cod' => 'COD'];
            $paymentStatusLabels = ['unpaid' => 'Menunggu pembayaran', 'submitted' => 'Menunggu verifikasi', 'due_on_pickup' => 'Bayar saat pengambilan', 'paid' => 'Pembayaran diterima'];
            $steps = [
                'confirmed' => 'PO Dikonfirmasi',
                'purchased' => 'Titipan Dibeli',
                'in_transit' => 'Dalam Perjalanan',
                'ready_for_pickup' => 'Siap Diambil',
                'completed' => 'Selesai',
            ];
        @endphp

        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-extrabold uppercase tracking-[0.2em] text-[#B5502E]">Area Pembeli</p>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Pesanan Saya</h1>
                <p class="mt-2 text-sm text-gray-600">Pantau perjalanan titipan, pembayaran, dan titik pengambilannya.</p>
            </div>
            <a href="{{ route('katalog') }}" class="inline-flex items-center justify-center gap-2 self-start rounded-lg bg-[#B5502E] px-5 py-3 text-sm font-bold text-white hover:bg-[#9E4527]">Cari Titipan <span aria-hidden="true">→</span></a>
        </div>

        @if (session('status'))
            <div role="status" class="mb-5 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->has('order'))
            <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first('order') }}</div>
        @endif

        <nav aria-label="Filter status pesanan" class="mb-5 flex gap-6 overflow-x-auto border-b border-[#EADBD1] text-sm">
            @foreach ($filters as $key => $label)
                <a href="{{ route('pesanan.index', ['filter' => $key]) }}" class="flex shrink-0 items-center gap-2 border-b-2 px-1 pb-3 font-bold {{ $filter === $key ? 'border-[#B5502E] text-[#B5502E]' : 'border-transparent text-gray-500 hover:text-gray-800' }}">
                    {{ $label }}
                    <span class="rounded-full px-2 py-0.5 text-[10px] {{ $filter === $key ? 'bg-[#F5EBE4] text-[#B5502E]' : 'bg-gray-100 text-gray-600' }}">{{ $counts[$key] }}</span>
                </a>
            @endforeach
        </nav>

        <div class="space-y-6">
            @forelse ($orders as $order)
                @php
                    $currentStep = array_search($order->status, array_keys($steps), true);
                    $jastiperName = $order->trip?->jastiper?->name ?? (config('catalog.products')[$order->product_id]['jastiper'] ?? 'Jastiper Kampung');
                    $jastiperEmail = $order->trip?->jastiper?->email;
                    $isCancelled = $order->status === 'cancelled';
                @endphp
                <article class="overflow-hidden rounded-2xl border border-[#EADBD1] bg-white shadow-sm">
                    <div class="flex flex-col gap-3 bg-[#FCEAE4] px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-[#B5502E] px-3 py-1 text-[10px] font-extrabold uppercase tracking-wide text-white">PO #JK-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-sm font-extrabold text-gray-900">{{ $order->route }}</span>
                            </div>
                            <p class="mt-1 text-xs text-gray-600">Dibuat {{ $order->created_at->format('d M Y, H:i') }}</p>
                        </div>
                        <span class="inline-flex w-fit items-center gap-2 rounded-full bg-white/80 px-3 py-1.5 text-xs font-bold {{ $statusClasses[$order->status] ?? 'text-gray-700' }}">
                            <span class="h-1.5 w-1.5 rounded-full bg-current"></span>{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-3 bg-[#FFF5F1] px-5 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div>
                            <p class="font-extrabold text-gray-900">{{ $order->product_title }} <span class="text-sm font-semibold text-gray-600">({{ $order->quantity }} item)</span></p>
                            <p class="mt-1 text-xs text-gray-600">Jastiper: {{ $jastiperName }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 sm:justify-end">
                            <p class="text-lg font-extrabold text-[#B5502E]">Rp {{ number_format($order->total_price, 0, ',', '.') }}</p>
                            @if ($order->payment_method)
                                <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-gray-700">{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }} · {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}</span>
                            @endif
                        </div>
                    </div>

                    @if ($isCancelled)
                        <div class="m-5 rounded-xl border border-gray-200 bg-gray-50 p-4 text-sm text-gray-600 sm:m-6">Pesanan ini dibatalkan dan tidak dilanjutkan.</div>
                    @else
                        <section class="px-5 py-5 sm:px-6">
                            <div class="mb-5 flex flex-wrap items-center justify-between gap-2">
                                <h2 class="text-xs font-extrabold uppercase tracking-[0.12em] text-[#8C5D42]">Status Perjalanan Titipan</h2>
                                <span class="text-[10px] font-semibold text-gray-500">Status diperbarui oleh jastiper</span>
                            </div>
                            @if ($order->status === 'pending')
                                <div class="mb-4 rounded-lg bg-amber-50 px-4 py-3 text-xs font-semibold text-amber-900">{{ $order->payment_status === 'submitted' ? 'Konfirmasi pembayaran sedang menunggu pemeriksaan jastiper.' : ($order->payment_status === 'unpaid' ? 'Selesaikan atau koordinasikan pembayaran agar jastiper dapat memproses pesanan.' : 'Pesanan COD menunggu konfirmasi jastiper.') }}</div>
                            @endif
                            <ol class="grid grid-cols-5 gap-2 overflow-x-auto">
                                @foreach ($steps as $stepStatus => $stepLabel)
                                    @php
                                        $stepIndex = array_search($stepStatus, array_keys($steps), true);
                                        $isComplete = $currentStep !== false && $currentStep >= $stepIndex;
                                        $isCurrent = $order->status === $stepStatus;
                                    @endphp
                                    <li class="min-w-[5.5rem] text-center">
                                        <span class="mx-auto flex h-8 w-8 items-center justify-center rounded-full text-xs font-extrabold {{ $isComplete ? 'bg-[#B5502E] text-white' : 'bg-[#FCEAE4] text-[#BFA99D]' }}">
                                            @if ($isComplete && ! $isCurrent) ✓ @else {{ $stepIndex + 1 }} @endif
                                        </span>
                                        <span class="mt-2 block text-[10px] leading-tight {{ $isCurrent ? 'font-extrabold text-[#B5502E]' : ($isComplete ? 'font-bold text-gray-800' : 'font-medium text-gray-400') }}">{{ $stepLabel }}</span>
                                        @if ($isCurrent)
                                            <span class="mt-1 inline-block rounded-full bg-[#FCEAE4] px-2 py-0.5 text-[9px] font-bold text-[#B5502E]">Sedang Berlangsung</span>
                                        @elseif ($isComplete)
                                            <span class="mt-1 block text-[9px] font-semibold text-emerald-700">Selesai</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ol>
                        </section>

                        <div class="grid gap-4 border-t border-[#F5EBE4] bg-[#FFFCFA] p-5 sm:grid-cols-2 sm:p-6">
                            <section class="rounded-xl border border-[#F1DED4] bg-white p-4">
                                <h3 class="flex items-center gap-2 text-sm font-extrabold text-gray-900"><span class="text-[#B5502E]">⌖</span> Detail Titik Kumpul & Pengambilan</h3>
                                <p class="mt-2 text-xs leading-relaxed text-gray-600">Lokasi: <span class="font-bold text-gray-900">{{ $order->pickup_point }}</span></p>
                                <p class="mt-3 rounded-lg bg-[#FCEAE4] px-3 py-2 text-xs text-gray-700">Tunjukkan ID pesanan <span class="font-extrabold tracking-wider text-[#B5502E]">JK-{{ str_pad((string) $order->id, 5, '0', STR_PAD_LEFT) }}</span> saat mengambil titipan.</p>
                                @if ($order->payment_method === 'cod')
                                    <p class="mt-3 text-[11px] leading-relaxed text-gray-500">Pembayaran COD dilakukan langsung kepada jastiper di titik temu.</p>
                                @endif
                            </section>
                            <section class="rounded-xl border border-[#F1DED4] bg-white p-4">
                                <h3 class="flex items-center gap-2 text-sm font-extrabold text-gray-900"><span class="text-[#B5502E]">◉</span> Bantuan & Jastiper</h3>
                                <p class="mt-2 text-xs leading-relaxed text-gray-600">Koordinasikan jadwal pengambilan dengan jastiper sebelum datang.</p>
                                <div class="mt-3 flex items-center gap-3 rounded-lg bg-[#FFF5F1] p-3">
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-[#B5502E] text-xs font-extrabold text-white">{{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($jastiperName, 0, 1)) }}</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-xs font-extrabold text-gray-900">{{ $jastiperName }}</p>
                                        @if ($jastiperEmail)
                                            <a href="mailto:{{ $jastiperEmail }}" class="break-all text-[10px] font-semibold text-[#B5502E] hover:underline">{{ $jastiperEmail }}</a>
                                        @else
                                            <p class="text-[10px] text-gray-500">Kontak jastiper belum tersedia di aplikasi.</p>
                                        @endif
                                    </div>
                                </div>
                                @if ($order->checkout_reference)
                                    <a href="{{ route('payment.show', $order->checkout_reference) }}" class="mt-3 inline-flex w-full items-center justify-center rounded-lg border border-[#EADBD1] px-3 py-2 text-xs font-bold text-gray-700 hover:bg-[#FDF8F5]">Lihat Detail Pembayaran</a>
                                @endif
                            </section>
                        </div>
                    @endif

                    @if ($order->status === 'pending' && in_array($order->payment_status, ['unpaid', 'due_on_pickup'], true))
                        <form method="POST" action="{{ route('pesanan.cancel', $order) }}" class="border-t border-[#F5EBE4] px-5 py-3 sm:px-6" onsubmit="return confirm('Batalkan pesanan ini?')">
                            @csrf
                            @method('PATCH')
                            <button class="text-xs font-bold text-red-600 hover:text-red-800">Batalkan pesanan</button>
                        </form>
                    @endif
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-[#DCC4B5] bg-white px-6 py-16 text-center">
                    <h2 class="text-xl font-extrabold text-gray-900">Belum ada pesanan di kategori ini</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm text-gray-600">Pesananmu akan muncul di sini setelah kamu checkout dari katalog.</p>
                    <a href="{{ route('katalog') }}" class="mt-6 inline-flex rounded-lg bg-[#B5502E] px-5 py-3 text-sm font-bold text-white hover:bg-[#9E4527]">Lihat Katalog</a>
                </div>
            @endforelse
        </div>

        <div class="mt-6">{{ $orders->links() }}</div>
    </div>
</x-app-layout>
