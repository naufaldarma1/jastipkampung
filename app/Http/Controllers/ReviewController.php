<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== 'completed') {
            return back()->withErrors([
                'order' => 'Penilaian hanya dapat diberikan setelah pesanan selesai diserahterimakan.',
            ]);
        }

        if ($order->review()->exists()) {
            return back()->withErrors([
                'order' => 'Pesanan ini sudah pernah Anda beri penilaian.',
            ]);
        }

        $validated = $request->validate([
            'product_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'store_rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $order->loadMissing('trip');

        $jastiperId = null;
        $jastiperSlug = null;

        if ($order->trip_id && $order->trip) {
            $jastiperId = $order->trip->user_id;
            $jastiperSlug = 'u-'.$jastiperId;
        } else {
            $catalogItem = config('catalog.products')[$order->product_id] ?? null;
            $jastiperSlug = $catalogItem['jastiper_slug'] ?? null;
        }

        Review::create([
            'order_id' => $order->id,
            'user_id' => $request->user()->id,
            'jastiper_id' => $jastiperId,
            'jastiper_slug' => $jastiperSlug,
            'trip_id' => $order->trip_id,
            'product_id' => $order->trip_id ? null : $order->product_id,
            'product_title' => $order->product_title,
            'product_rating' => $validated['product_rating'],
            'store_rating' => $validated['store_rating'],
            'comment' => $validated['comment'] ?? null,
        ]);

        return back()->with('status', 'Terima kasih! Penilaian produk dan toko jastiper berhasil dikirim.');
    }
}
