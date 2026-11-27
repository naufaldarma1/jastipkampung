<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CartController extends Controller
{
    public function add(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:sample,trip'],
            'id' => ['required', 'integer', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        $item = $this->resolveItem($validated['type'], (int) $validated['id'], $request);
        $cart = $request->session()->get('cart', []);
        $key = $validated['type'].'-'.$validated['id'];
        $quantity = ($cart[$key]['quantity'] ?? 0) + (int) $validated['quantity'];

        if ($quantity > $item['available']) {
            throw ValidationException::withMessages([
                'quantity' => 'Sisa kuota '.$item['available'].' '.$item['unit'].'.',
            ]);
        }

        $cart[$key] = ['type' => $validated['type'], 'id' => (int) $validated['id'], 'quantity' => $quantity];
        $request->session()->put('cart', $cart);

        return back()->with('status', $item['title'].' ditambahkan ke keranjang.');
    }

    public function index(Request $request): View|RedirectResponse
    {
        $items = $this->cartItems($request);

        return view('cart.index', [
            'items' => $items,
            'total' => $items->sum('total_price'),
            'itemCount' => $items->sum('quantity'),
        ]);
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $validated = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:100']]);
        $cart = $request->session()->get('cart', []);
        abort_unless(isset($cart[$key]), 404);

        [$type, $id] = $this->parseKey($key);
        $item = $this->resolveItem($type, $id, $request);

        if ($validated['quantity'] > $item['available']) {
            throw ValidationException::withMessages(['quantity' => 'Sisa kuota '.$item['available'].' '.$item['unit'].'.']);
        }

        $cart[$key]['quantity'] = (int) $validated['quantity'];
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Jumlah di keranjang diperbarui.');
    }

    public function remove(Request $request, string $key): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$key]);
        $request->session()->put('cart', $cart);

        return back()->with('status', 'Titipan dihapus dari keranjang.');
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $items = $this->cartItems($request);

        if ($items->isEmpty()) {
            return redirect()->route('katalog')->withErrors(['cart' => 'Keranjangmu masih kosong.']);
        }

        $pickupPointOptions = $this->pickupPointOptions($items);
        $defaultPickupPoint = $items->first()['pickup_point'] ?? ($pickupPointOptions[0]['name'] ?? '');

        return view('checkout.index', [
            'items' => $items,
            'total' => $items->sum('total_price'),
            'itemCount' => $items->sum('quantity'),
            'pickupPointOptions' => $pickupPointOptions,
            'selectedPickupPoint' => $request->old('pickup_point', $defaultPickupPoint),
        ]);
    }

    public function placeOrder(Request $request): RedirectResponse
    {
        $items = $this->cartItems($request);

        if ($items->isEmpty()) {
            return redirect()->route('katalog')->withErrors(['cart' => 'Keranjangmu masih kosong.']);
        }

        $allowedPickupPoints = collect($this->pickupPointOptions($items))->pluck('name')->all();

        $validated = $request->validate([
            'payment_method' => ['required', 'in:qris,bank_transfer,cod'],
            'pickup_point' => ['required', 'string', 'max:150', Rule::in($allowedPickupPoints)],
        ], [
            'pickup_point.required' => 'Pilih satu titik temu resmi untuk pesananmu.',
            'pickup_point.in' => 'Titik temu yang dipilih tidak tersedia.',
        ]);

        $reference = (string) Str::uuid();

        DB::transaction(function () use ($request, $items, $validated, $reference) {
            foreach ($items as $item) {
                $this->assertAvailable($item, $request);

                Order::create([
                    'user_id' => $request->user()->id,
                    'trip_id' => $item['type'] === 'trip' ? $item['id'] : null,
                    'product_id' => $item['type'] === 'sample' ? $item['id'] : null,
                    'product_title' => $item['title'],
                    'route' => $item['route'],
                    'pickup_point' => $validated['pickup_point'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'service_fee' => $item['service_fee'],
                    'total_price' => $item['total_price'],
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => $validated['payment_method'] === 'cod' ? 'due_on_pickup' : 'unpaid',
                    'checkout_reference' => $reference,
                    'status' => 'pending',
                ]);
            }
        });

        $request->session()->forget('cart');

        return redirect()->route('payment.show', $reference);
    }

    private function cartItems(Request $request)
    {
        $items = collect();

        foreach ($request->session()->get('cart', []) as $key => $cartItem) {
            try {
                $item = $this->resolveItem($cartItem['type'], (int) $cartItem['id'], $request);
                $item['key'] = $key;
                $item['quantity'] = (int) $cartItem['quantity'];
                $item['total_price'] = ($item['unit_price'] + $item['service_fee']) * $item['quantity'];
                $items->push($item);
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException|\Symfony\Component\HttpKernel\Exception\HttpException) {
                // Stale cart entries are omitted; checkout revalidates availability before saving.
            }
        }

        return $items;
    }

    private function resolveItem(string $type, int $id, Request $request): array
    {
        if ($type === 'trip') {
            $trip = Trip::withSum(['orders as reserved_quantity' => fn ($query) => $query->where('status', '!=', 'cancelled')], 'quantity')->findOrFail($id);
            abort_if($trip->user_id === $request->user()->id, 403, 'Jastiper tidak dapat memesan PO sendiri.');
            abort_unless($trip->status === 'open' && $trip->order_deadline->isFuture(), 404);

            return [
                'type' => 'trip', 'id' => $trip->id, 'title' => $trip->title,
                'route' => $trip->origin.' → '.$trip->destination,
                'pickup_point' => $trip->pickup_point, 'unit' => $trip->unit,
                'unit_price' => $trip->price, 'service_fee' => $trip->service_fee,
                'available' => max(0, $trip->quota - (int) $trip->reserved_quantity),
            ];
        }

        $product = config('catalog.products')[$id] ?? null;
        abort_unless($product, 404);
        $reserved = Order::query()->whereNull('trip_id')->where('product_id', $id)->where('status', '!=', 'cancelled')->sum('quantity');

        return [
            'type' => 'sample', 'id' => $id, 'title' => $product['title'],
            'route' => $product['rute'], 'pickup_point' => $product['kumpul'],
            'unit' => $product['satuan_kuota'], 'unit_price' => $product['harga'],
            'service_fee' => $product['fee'],
            'available' => max(0, $product['total'] - $product['terisi'] - $reserved),
        ];
    }

    private function assertAvailable(array $item, Request $request): void
    {
        $fresh = $this->resolveItem($item['type'], $item['id'], $request);

        if ($item['quantity'] > $fresh['available']) {
            throw ValidationException::withMessages([
                'cart' => 'Kuota untuk '.$item['title'].' berubah. Sisa '.$fresh['available'].' '.$fresh['unit'].'.',
            ]);
        }
    }

    private function pickupPointOptions(Collection $items): array
    {
        $options = collect(config('catalog.pickup_points', []))
            ->keyBy('name')
            ->map(fn (array $point) => [...$point, 'recommended' => false])
            ->all();

        foreach ($items as $item) {
            $point = trim((string) ($item['pickup_point'] ?? ''));

            if ($point === '') {
                continue;
            }

            if (isset($options[$point])) {
                $options[$point]['recommended'] = true;
            } else {
                $options[$point] = [
                    'name' => $point,
                    'description' => 'Titik temu yang ditetapkan jastiper untuk rute '.$item['route'].'.',
                    'badge' => 'Rekomendasi jastiper',
                    'recommended' => true,
                ];
            }
        }

        return array_values($options);
    }

    private function parseKey(string $key): array
    {
        abort_unless(preg_match('/^(sample|trip)-(\d+)$/', $key, $matches), 404);

        return [$matches[1], (int) $matches[2]];
    }
}
