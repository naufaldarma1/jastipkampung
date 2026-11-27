<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function show(Request $request, string $reference): View
    {
        $orders = $this->ownedOrders($request, $reference);

        abort_if($orders->isEmpty(), 404);

        return view('checkout.payment', [
            'orders' => $orders,
            'reference' => $reference,
            'total' => $orders->sum('total_price'),
            'paymentMethod' => $orders->first()->payment_method,
        ]);
    }

    public function confirm(Request $request, string $reference): RedirectResponse
    {
        $orders = $this->ownedOrders($request, $reference);
        abort_if($orders->isEmpty(), 404);

        DB::transaction(function () use ($orders) {
            foreach ($orders as $order) {
                if ($order->payment_method === 'cod') {
                    $order->update(['payment_status' => 'due_on_pickup']);
                } elseif ($order->payment_status === 'unpaid') {
                    $order->update(['payment_status' => 'submitted']);
                }
            }
        });

        return redirect()->route('payment.receipt', $reference);
    }

    public function receipt(Request $request, string $reference): View
    {
        $orders = $this->ownedOrders($request, $reference);
        abort_if($orders->isEmpty(), 404);

        return view('checkout.receipt', [
            'orders' => $orders,
            'reference' => $reference,
            'total' => $orders->sum('total_price'),
        ]);
    }

    public function verify(Request $request, Order $order): RedirectResponse
    {
        $order->loadMissing('trip');
        abort_unless($order->trip && $order->trip->user_id === $request->user()->id, 403);
        abort_unless($order->payment_status === 'submitted', 409, 'Pembayaran ini belum menunggu verifikasi.');

        $order->update(['payment_status' => 'paid']);

        return back()->with('status', 'Pembayaran pesanan #'.$order->id.' telah ditandai diterima.');
    }

    private function ownedOrders(Request $request, string $reference)
    {
        if (! Str::isUuid($reference)) {
            return collect();
        }

        return Order::query()
            ->where('user_id', $request->user()->id)
            ->where('checkout_reference', $reference)
            ->latest()
            ->get();
    }
}
