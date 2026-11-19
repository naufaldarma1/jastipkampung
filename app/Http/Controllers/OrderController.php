<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $userOrders = $request->user()->orders();
        $counts = [
            'semua' => (clone $userOrders)->count(),
            'perjalanan' => (clone $userOrders)->whereIn('status', ['confirmed', 'purchased', 'in_transit', 'ready_for_pickup'])->count(),
            'diproses' => (clone $userOrders)->where('status', 'pending')->count(),
            'selesai' => (clone $userOrders)->where('status', 'completed')->count(),
            'dibatalkan' => (clone $userOrders)->where('status', 'cancelled')->count(),
        ];
        $filter = $request->query('filter', 'semua');
        $filterStatuses = [
            'perjalanan' => ['confirmed', 'purchased', 'in_transit', 'ready_for_pickup'],
            'diproses' => ['pending'],
            'selesai' => ['completed'],
            'dibatalkan' => ['cancelled'],
        ];
        $query = $request->user()->orders()->with('trip.jastiper')->latest();

        if (isset($filterStatuses[$filter])) {
            $query->whereIn('status', $filterStatuses[$filter]);
        } elseif ($filter !== 'semua') {
            $filter = 'semua';
        }

        return view('orders.index', [
            'orders' => $query->paginate(10)->withQueryString(),
            'counts' => $counts,
            'filter' => $filter,
        ]);
    }

    public function store(Request $request, int $product): RedirectResponse
    {
        $items = config('catalog.products', []);
        abort_unless(array_key_exists($product, $items), 404);

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);
        $item = $items[$product];

        $order = DB::transaction(function () use ($request, $product, $validated, $item) {
            $reserved = Order::where('product_id', $product)
                ->where('status', '!=', 'cancelled')
                ->sum('quantity');
            $available = $item['total'] - $item['terisi'] - $reserved;

            if ($validated['quantity'] > $available) {
                throw ValidationException::withMessages([
                    'quantity' => 'Kuota tersisa hanya '.$available.' '.$item['satuan_kuota'].'.',
                ]);
            }

            return Order::create([
                'user_id' => $request->user()->id,
                'product_id' => $product,
                'product_title' => $item['title'],
                'route' => $item['rute'],
                'pickup_point' => $item['kumpul'],
                'quantity' => $validated['quantity'],
                'unit_price' => $item['harga'],
                'service_fee' => $item['fee'],
                'total_price' => ($item['harga'] + $item['fee']) * $validated['quantity'],
                'status' => 'pending',
            ]);
        });

        return redirect()->route('pesanan.index')->with('status', 'Pesanan #'.$order->id.' berhasil dibuat. Silakan koordinasi dengan jastiper untuk pembayaran.');
    }

    public function cancel(Request $request, Order $order): RedirectResponse
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        if ($order->status !== 'pending' || ! in_array($order->payment_status, ['unpaid', 'due_on_pickup'], true)) {
            return back()->withErrors(['order' => 'Pesanan ini tidak dapat dibatalkan dari halaman ini. Hubungi jastiper jika pembayaran sudah dikirim.']);
        }

        $order->update(['status' => 'cancelled']);

        return back()->with('status', 'Pesanan berhasil dibatalkan.');
    }
}
