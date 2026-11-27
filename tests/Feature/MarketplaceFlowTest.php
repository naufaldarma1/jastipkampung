<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketplaceFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_pages_and_healthcheck_render_cleanly(): void
    {
        $this->get('/')->assertOk()->assertSee('Jastip');
        $this->get('/up')->assertOk();
        $this->get('/jadi-jastiper')->assertRedirect(route('register', ['role' => 'jastiper']));
        $this->get('/katalog')->assertRedirect(route('login'));
    }

    public function test_authenticated_users_visiting_guest_auth_pages_are_redirected_by_role(): void
    {
        $pembeli = User::factory()->create(['role' => 'pembeli']);
        $jastiper = User::factory()->create(['role' => 'jastiper']);

        $this->actingAs($pembeli)->get('/login')->assertRedirect(route('katalog'));
        $this->actingAs($jastiper)->get('/login')->assertRedirect(route('dashboard.jastiper'));

        $this->actingAs($pembeli)->get('/dashboard')->assertRedirect(route('dashboard.pembeli'));
        $this->actingAs($jastiper)->get('/dashboard')->assertRedirect(route('dashboard.jastiper'));
    }

    public function test_role_middleware_enforces_strict_access_boundaries(): void
    {
        $pembeli = User::factory()->create(['role' => 'pembeli']);
        $jastiper = User::factory()->create(['role' => 'jastiper']);

        $this->actingAs($pembeli)->get('/dashboard-pembeli')->assertOk();
        $this->actingAs($pembeli)->get('/dashboard-jastiper')->assertForbidden();

        $this->actingAs($jastiper)->get('/dashboard-jastiper')->assertOk();
        $this->actingAs($jastiper)->get('/dashboard-pembeli')->assertForbidden();
        $this->actingAs($jastiper)->get('/keranjang')->assertForbidden();

        // Katalog renders cleanly for Jastiper without 403 cart form
        $this->actingAs($jastiper)
            ->get('/katalog')
            ->assertOk()
            ->assertSee('Mode Jastiper');
    }

    public function test_pembeli_can_upgrade_account_to_jastiper(): void
    {
        $pembeli = User::factory()->create(['role' => 'pembeli']);

        $this->actingAs($pembeli)->get('/jadi-jastiper')->assertOk();

        $this->actingAs($pembeli)
            ->post('/jadi-jastiper')
            ->assertRedirect(route('dashboard.jastiper'));

        $this->assertSame('jastiper', $pembeli->fresh()->role);
    }

    public function test_katalog_filter_and_search_work(): void
    {
        $pembeli = User::factory()->create(['role' => 'pembeli']);

        $this->actingAs($pembeli)
            ->get('/katalog?filter=Solo')
            ->assertOk()
            ->assertSee('Serabi Notosuman')
            ->assertDontSee('Jenang Kudus Mubarok');

        $this->actingAs($pembeli)
            ->get('/katalog?search=Mendoan')
            ->assertOk()
            ->assertSee('Mendoan Mentah Baturraden');
    }

    public function test_full_cart_checkout_qris_payment_and_cancel_rules(): void
    {
        $pembeli = User::factory()->create(['role' => 'pembeli']);

        // Exceeding sample product 0 quota (4 slots left) fails validation
        $this->actingAs($pembeli)
            ->post(route('cart.add'), ['type' => 'sample', 'id' => 0, 'quantity' => 10])
            ->assertSessionHasErrors('quantity');

        // Add valid quantity to cart
        $this->actingAs($pembeli)
            ->post(route('cart.add'), ['type' => 'sample', 'id' => 0, 'quantity' => 2])
            ->assertSessionHasNoErrors();

        // Update cart quantity to 1
        $this->actingAs($pembeli)
            ->patch(route('cart.update', 'sample-0'), ['quantity' => 1])
            ->assertSessionHasNoErrors();

        $this->actingAs($pembeli)
            ->get(route('cart.index'))
            ->assertOk()
            ->assertSee('Rp 40.000');

        $this->actingAs($pembeli)
            ->get(route('checkout.index'))
            ->assertOk()
            ->assertSee('Depan GSG UNNES');

        // Place QRIS order with another chosen official pickup point
        $checkoutResponse = $this->actingAs($pembeli)->post(route('checkout.store'), [
            'payment_method' => 'qris',
            'pickup_point' => 'Bundaran Konservasi UNNES',
        ]);

        $order = Order::firstOrFail();
        $this->assertSame('Bundaran Konservasi UNNES', $order->pickup_point);
        $this->assertSame('unpaid', $order->payment_status);
        $this->assertSame(40000, $order->total_price);
        $checkoutResponse->assertRedirect(route('payment.show', $order->checkout_reference));

        // Payment page & confirmation
        $this->actingAs($pembeli)
            ->get(route('payment.show', $order->checkout_reference))
            ->assertOk()
            ->assertSee('Bayar dengan QRIS');

        $this->actingAs($pembeli)
            ->post(route('payment.confirm', $order->checkout_reference))
            ->assertRedirect(route('payment.receipt', $order->checkout_reference));

        $this->assertSame('submitted', $order->fresh()->payment_status);

        // Submitted payment order cannot be self-cancelled
        $this->actingAs($pembeli)
            ->patch(route('pesanan.cancel', $order))
            ->assertSessionHasErrors('order');

        // Receipt & order tracker render cleanly
        $this->actingAs($pembeli)
            ->get(route('payment.receipt', $order->checkout_reference))
            ->assertOk()
            ->assertSee('Konfirmasi pembayaran terkirim');

        $this->actingAs($pembeli)
            ->get(route('pesanan.index'))
            ->assertOk()
            ->assertSee('Serabi Notosuman');
    }

    public function test_full_jastiper_trip_lifecycle_payment_verification_and_five_step_fulfillment(): void
    {
        $jastiper = User::factory()->create(['role' => 'jastiper', 'name' => 'Jastiper Solo']);
        $pembeli = User::factory()->create(['role' => 'pembeli', 'name' => 'Pembeli Sekaran']);

        // Jastiper creates a PO trip
        $this->actingAs($jastiper)
            ->post(route('jastiper.trips.store'), [
                'title' => 'Gudeg Ceker Margoyudan',
                'origin' => 'Solo',
                'destination' => 'Sekaran, UNNES',
                'description' => 'Berangkat Minggu malam.',
                'quota' => 10,
                'unit' => 'porsi',
                'price' => 25000,
                'service_fee' => 5000,
                'order_deadline' => now()->addDays(2)->format('Y-m-d\TH:i'),
                'return_at' => now()->addDays(3)->format('Y-m-d\TH:i'),
                'pickup_point' => 'Depan GSG UNNES',
            ])
            ->assertRedirect(route('dashboard.jastiper'));

        $trip = Trip::firstOrFail();

        // Jastiper cannot order their own PO
        $this->actingAs($jastiper)
            ->post(route('trips.order.store', $trip), [
                'quantity' => 1,
                'payment_method' => 'cod',
            ])
            ->assertForbidden();

        // Pembeli orders via cart + checkout QRIS
        $this->actingAs($pembeli)->post(route('cart.add'), [
            'type' => 'trip',
            'id' => $trip->id,
            'quantity' => 2,
        ])->assertSessionHasNoErrors();

        $this->actingAs($pembeli)->post(route('checkout.store'), [
            'payment_method' => 'qris',
            'pickup_point' => 'Depan GSG UNNES',
        ]);

        $order = Order::where('trip_id', $trip->id)->firstOrFail();

        // Jastiper cannot confirm order before payment is verified
        $this->actingAs($jastiper)
            ->patch(route('jastiper.orders.status', $order), ['status' => 'confirmed'])
            ->assertSessionHasErrors('order');

        // Pembeli submits payment confirmation, Jastiper verifies payment
        $this->actingAs($pembeli)->post(route('payment.confirm', $order->checkout_reference));
        $this->actingAs($jastiper)
            ->patch(route('jastiper.orders.payment.verify', $order))
            ->assertSessionHasNoErrors();

        $this->assertSame('paid', $order->fresh()->payment_status);

        // Sequential 5-step transitions enforced
        $this->actingAs($jastiper)
            ->patch(route('jastiper.orders.status', $order), ['status' => 'in_transit'])
            ->assertSessionHasErrors('order');

        foreach (['confirmed', 'purchased', 'in_transit', 'ready_for_pickup', 'completed'] as $nextStatus) {
            $this->actingAs($jastiper)
                ->patch(route('jastiper.orders.status', $order), ['status' => $nextStatus])
                ->assertSessionHasNoErrors();

            $this->assertSame($nextStatus, $order->fresh()->status);
        }

        // Jastiper can close and reopen active PO
        $this->actingAs($jastiper)
            ->patch(route('jastiper.trips.status', $trip), ['status' => 'closed'])
            ->assertSessionHasNoErrors();
        $this->assertSame('closed', $trip->fresh()->status);

        $this->actingAs($jastiper)
            ->patch(route('jastiper.trips.status', $trip), ['status' => 'open'])
            ->assertSessionHasNoErrors();
        $this->assertSame('open', $trip->fresh()->status);
    }

    public function test_checkout_allows_selecting_single_official_pickup_point_for_multiple_cart_items(): void
    {
        $pembeli = User::factory()->create(['role' => 'pembeli']);

        // Add two sample items with different jastiper pickup points
        $this->actingAs($pembeli)->post(route('cart.add'), ['type' => 'sample', 'id' => 1, 'quantity' => 1])->assertSessionHasNoErrors();
        $this->actingAs($pembeli)->post(route('cart.add'), ['type' => 'sample', 'id' => 3, 'quantity' => 1])->assertSessionHasNoErrors();

        $response = $this->actingAs($pembeli)->get(route('checkout.index'));
        $response->assertOk()
            ->assertSee('name="pickup_point"', false)
            ->assertDontSee('name="pickup_points[', false)
            ->assertSee('Depan GSG UNNES')
            ->assertSee('Simpang Banaran UNNES')
            ->assertSee('Bundaran Konservasi UNNES')
            ->assertSee('Lapangan Rektorat UNNES')
            ->assertSee('Sekitar FTIK');

        preg_match_all('/<input[^>]+name="pickup_point"[^>]*>/', $response->getContent(), $radioInputs);
        $checkedRadios = array_filter($radioInputs[0] ?? [], fn (string $tag) => str_contains($tag, 'checked'));
        $this->assertCount(1, $checkedRadios);

        // Reject arbitrary pickup point not in allowed list
        $this->actingAs($pembeli)
            ->post(route('checkout.store'), [
                'payment_method' => 'cod',
                'pickup_point' => 'Lokasi Sembarang',
            ])
            ->assertSessionHasErrors('pickup_point');

        // Accept valid chosen pickup point and persist to all created orders
        $this->actingAs($pembeli)
            ->post(route('checkout.store'), [
                'payment_method' => 'cod',
                'pickup_point' => 'Depan GSG UNNES',
            ])
            ->assertRedirect();

        $orders = Order::all();
        $this->assertCount(2, $orders);
        $this->assertTrue($orders->every(fn (Order $order) => $order->pickup_point === 'Depan GSG UNNES'));
    }
}
