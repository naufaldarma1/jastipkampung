<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TripOrderController extends Controller
{
    public function store(Request $request, Trip $trip): RedirectResponse
    {
        abort_if($trip->user_id === $request->user()->id, 403, 'Jastiper tidak dapat memesan PO sendiri.');
        abort_unless($trip->status === 'open' && $trip->order_deadline->isFuture(), 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
            'payment_method' => ['required', 'in:qris,bank_transfer,cod'],
        ]);

        $order = DB::transaction(function () use ($request, $trip, $validated) {
            $reserved = $trip->orders()
                ->where('status', '!=', 'cancelled')
                ->sum('quantity');
            $available = max(0, $trip->quota - $reserved);

            if ($validated['quantity'] > $available) {
                throw ValidationException::withMessages([
                    'quantity' => 'Sisa kuota PO ini '.$available.' '.$trip->unit.'.',
                ]);
            }

            return Order::create([
                'user_id' => $request->user()->id,
                'trip_id' => $trip->id,
                'product_id' => null,
                'product_title' => $trip->title,
                'route' => $trip->origin.' → '.$trip->destination,
                'pickup_point' => $trip->pickup_point,
                'quantity' => $validated['quantity'],
                'unit_price' => $trip->price,
                'service_fee' => $trip->service_fee,
                'total_price' => ($trip->price + $trip->service_fee) * $validated['quantity'],
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
            ]);
        });

        return redirect()->route('pesanan.index')->with('status', 'Permintaan titip #'.$order->id.' terkirim. Jastiper akan mengonfirmasi pesananmu.');
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $order->loadMissing('trip');
        abort_unless($order->trip && $order->trip->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,purchased,in_transit,ready_for_pickup,completed,cancelled'],
        ]);

        $nextStatuses = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['purchased'],
            'purchased' => ['in_transit'],
            'in_transit' => ['ready_for_pickup'],
            'ready_for_pickup' => ['completed'],
        ];

        if (! in_array($validated['status'], $nextStatuses[$order->status] ?? [], true)) {
            return back()->withErrors(['order' => 'Tahapan pesanan harus diperbarui secara berurutan.']);
        }

        if ($validated['status'] === 'confirmed' && ! in_array($order->payment_status, ['paid', 'due_on_pickup'], true)) {
            return back()->withErrors(['order' => 'Konfirmasi pesanan hanya tersedia setelah pembayaran diverifikasi atau metode COD dipilih.']);
        }

        $order->update(['status' => $validated['status']]);

        $messages = [
            'confirmed' => 'Pesanan dikonfirmasi.',
            'purchased' => 'Titipan ditandai sudah dibeli.',
            'in_transit' => 'Titipan sedang dalam perjalanan.',
            'ready_for_pickup' => 'Titipan siap diambil pembeli.',
            'completed' => 'Pesanan ditandai selesai.',
            'cancelled' => 'Pesanan ditolak dan kuota dikembalikan.',
        ];

        return back()->with('status', $messages[$validated['status']]);
    }
}
