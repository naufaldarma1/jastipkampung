<x-app-layout>
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if (session('status'))
            <div role="status" class="mb-6 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm font-semibold text-green-800">{{ session('status') }}</div>
        @endif
        @if ($errors->has('order'))
            <div role="alert" class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ $errors->first('order') }}</div>
        @endif

        <div class="mb-8 flex flex-col gap-5 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="text-xs font-bold uppercase tracking-[0.2em] text-[#B5502E]">Area Jastiper</p>
                <h1 class="mt-2 text-3xl font-extrabold text-gray-900">Halo, {{ Auth::user()->name }}!</h1>
                <p class="mt-2 max-w-2xl text-sm leading-relaxed text-gray-600">Buat PO perjalananmu, tampilkan penawaran di katalog, lalu kelola permintaan titip dari pembeli.</p>
            </div>
            <a href="{{ route('jastiper.trips.create') }}" class="inline-flex h-fit self-start items-center justify-center whitespace-nowrap rounded-lg bg-[#B5502E] px-5 py-3 text-sm font-bold text-white hover:bg-[#9E4527]">+ Buat PO Baru</a>
        </div>

        <div class="mb-8 grid gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-[#EADBD1] bg-white p-5"><p class="text-sm font-semibold text-gray-500">Total PO</p><p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $trips->count() }}</p></div>
            <div class="rounded-xl border border-[#EADBD1] bg-white p-5"><p class="text-sm font-semibold text-gray-500">PO Aktif</p><p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $trips->where('status', 'open')->count() }}</p></div>
            <div class="rounded-xl border border-[#EADBD1] bg-white p-5"><p class="text-sm font-semibold text-gray-500">Permintaan masuk</p><p class="mt-2 text-2xl font-extrabold text-gray-900">{{ $trips->sum(fn ($trip) => $trip->orders->where('status', 'pending')->count()) }}</p></div>
        </div>

        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <h2 class="text-xl font-extrabold text-gray-900">PO perjalananmu</h2>
                <p class="mt-1 text-sm text-gray-600">Pembeli akan melihat PO aktif di katalog dan dapat mengajukan jumlah titipan.</p>
            </div>
        </div>

        <div class="space-y-5">
            @forelse ($trips as $trip)
                <article class="rounded-2xl border border-[#EADBD1] bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-extrabold text-gray-900">{{ $trip->title }}</h3>
                                <span class="rounded-full px-3 py-1 text-xs font-bold {{ $trip->status === 'open' ? 'bg-green-50 text-green-800' : 'bg-gray-100 text-gray-700' }}">{{ $trip->status === 'open' ? 'Tayang di katalog' : 'Ditutup' }}</span>
                            </div>
                            <p class="mt-2 text-sm font-semibold text-gray-600">{{ $trip->origin }} → {{ $trip->destination }}</p>
                            @if ($trip->description)
                                <p class="mt-2 text-sm text-gray-600">{{ $trip->description }}</p>
                            @endif
                        </div>
                        <div class="flex shrink-0 gap-2">
                            <form method="POST" action="{{ route('jastiper.trips.status', $trip) }}">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="{{ $trip->status === 'open' ? 'closed' : 'open' }}">
                                <button class="rounded-lg border border-[#EADBD1] px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-[#FDF8F5]">{{ $trip->status === 'open' ? 'Tutup PO' : 'Buka lagi' }}</button>
                            </form>
                        </div>
                    </div>

                    <dl class="mt-5 grid gap-4 border-t border-[#F5EBE4] pt-4 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div><dt class="text-xs font-semibold text-gray-500">Kuota terisi</dt><dd class="mt-1 font-bold text-gray-800">{{ (int) $trip->reserved_quantity }} / {{ $trip->quota }} {{ $trip->unit }}</dd></div>
                        <div><dt class="text-xs font-semibold text-gray-500">Harga per unit</dt><dd class="mt-1 font-bold text-gray-800">Rp {{ number_format($trip->price + $trip->service_fee, 0, ',', '.') }}</dd></div>
                        <div><dt class="text-xs font-semibold text-gray-500">Batas pemesanan</dt><dd class="mt-1 font-bold text-gray-800">{{ $trip->order_deadline->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</dd></div>
                        <div><dt class="text-xs font-semibold text-gray-500">Titik temu</dt><dd class="mt-1 font-bold text-gray-800">{{ $trip->pickup_point }}</dd></div>
                    </dl>

                    <div class="mt-5 border-t border-[#F5EBE4] pt-4">
                        <h4 class="font-extrabold text-gray-900">Permintaan titip ({{ $trip->orders->count() }})</h4>
                        @forelse ($trip->orders as $order)
                            <div class="mt-3 flex flex-col gap-3 rounded-xl bg-[#FDF8F5] p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="font-bold text-gray-900">{{ $order->user->name }} <span class="font-medium text-gray-600">· {{ $order->quantity }} {{ $trip->unit }}</span></p>
                                    <p class="mt-1 text-sm text-gray-600">Rp {{ number_format($order->total_price, 0, ',', '.') }} · {{ $order->user->email }}</p>
                                    @php
                                        $orderStatusLabels = ['pending' => 'Menunggu konfirmasi', 'confirmed' => 'Dikonfirmasi', 'purchased' => 'Sudah dibeli', 'in_transit' => 'Dalam perjalanan', 'ready_for_pickup' => 'Siap diambil', 'cancelled' => 'Ditolak / dibatalkan', 'completed' => 'Selesai'];
                                        $paymentLabels = ['qris' => 'QRIS', 'bank_transfer' => 'Transfer bank', 'cod' => 'COD'];
                                        $nextOrderStatuses = ['pending' => 'confirmed', 'confirmed' => 'purchased', 'purchased' => 'in_transit', 'in_transit' => 'ready_for_pickup', 'ready_for_pickup' => 'completed'];
                                        $nextOrderLabels = ['confirmed' => 'Konfirmasi', 'purchased' => 'Tandai sudah dibeli', 'in_transit' => 'Tandai dalam perjalanan', 'ready_for_pickup' => 'Tandai siap diambil', 'completed' => 'Selesaikan pesanan'];
                                    @endphp
                                    <p class="mt-1 text-xs font-bold {{ $order->status === 'pending' ? 'text-amber-700' : ($order->status === 'confirmed' ? 'text-blue-700' : 'text-gray-500') }}">{{ $orderStatusLabels[$order->status] ?? ucfirst($order->status) }}</p>
                                    @if ($order->payment_method)
                                        <p class="mt-1 text-xs text-gray-600">Pembayaran: {{ $paymentLabels[$order->payment_method] ?? $order->payment_method }} · {{ ['unpaid' => 'belum dibayar', 'submitted' => 'perlu diverifikasi', 'due_on_pickup' => 'COD saat pengambilan', 'paid' => 'diterima'][$order->payment_status] ?? $order->payment_status }}</p>
                                    @endif
                                </div>
                                @if (isset($nextOrderStatuses[$order->status]) && ($order->status !== 'pending' || in_array($order->payment_status, ['paid', 'due_on_pickup'], true)))
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('jastiper.orders.status', $order) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status" value="{{ $nextOrderStatuses[$order->status] }}">
                                            <button class="rounded-lg bg-[#B5502E] px-4 py-2 text-sm font-bold text-white hover:bg-[#9E4527]">{{ $nextOrderLabels[$nextOrderStatuses[$order->status]] }}</button>
                                        </form>
                                        @if ($order->status === 'pending')
                                            <form method="POST" action="{{ route('jastiper.orders.status', $order) }}" onsubmit="return confirm('Tolak permintaan ini?')">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="cancelled">
                                                <button class="rounded-lg border border-red-200 px-4 py-2 text-sm font-bold text-red-700 hover:bg-red-50">Tolak</button>
                                            </form>
                                        @endif
                                    </div>
                                @elseif ($order->status === 'pending' && $order->payment_status === 'unpaid')
                                    <p class="text-xs font-semibold text-amber-700">Menunggu pembayaran pembeli</p>
                                @elseif ($order->status === 'pending' && $order->payment_status === 'submitted')
                                    <p class="text-xs font-semibold text-amber-700">Verifikasi pembayaran sebelum mengonfirmasi pesanan</p>
                                @endif
                                @if ($order->payment_status === 'submitted')
                                    <form method="POST" action="{{ route('jastiper.orders.payment.verify', $order) }}" onsubmit="return confirm('Tandai pembayaran ini sudah diterima?')">
                                        @csrf
                                        @method('PATCH')
                                        <button class="rounded-lg border border-green-200 px-4 py-2 text-sm font-bold text-green-800 hover:bg-green-50">Tandai Pembayaran Diterima</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="mt-2 text-sm text-gray-500">Belum ada permintaan untuk PO ini.</p>
                        @endforelse
                    </div>
                </article>
            @empty
                <div class="rounded-2xl border border-dashed border-[#DCC4B5] bg-white px-6 py-16 text-center">
                    <h3 class="text-xl font-extrabold text-gray-900">Belum ada PO perjalanan</h3>
                    <p class="mx-auto mt-2 max-w-lg text-sm text-gray-600">Buat penawaran pertama dengan kota tujuan, produk, kuota, harga, batas pemesanan, jadwal kembali, dan titik temu.</p>
                    <a href="{{ route('jastiper.trips.create') }}" class="mt-6 inline-flex rounded-lg bg-[#B5502E] px-5 py-3 text-sm font-bold text-white hover:bg-[#9E4527]">Buat PO Pertama</a>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
