<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JastiperRatingTest extends TestCase
{
    use RefreshDatabase;

    public function test_katalog_displays_product_and_store_ratings(): void
    {
        $buyer = User::factory()->create(['role' => 'pembeli']);

        $response = $this->actingAs($buyer)->get('/katalog?sort=rating');

        $response->assertOk();
        $response->assertSee('4.9');
        $response->assertSee('penilaian');
        $response->assertSee('terjual');
        $response->assertSee('/jastiper/sinta-m', false);
    }

    public function test_public_sample_jastiper_store_profile_and_star_filter_work(): void
    {
        $response = $this->get('/jastiper/rizky-f');
        $response->assertOk();
        $response->assertSee('Rizky F.');
        $response->assertSee('Penilaian Toko');
        $response->assertSee('Serabi Notosuman');

        $filtered = $this->get('/jastiper/rizky-f?star=4');
        $filtered->assertOk();
        $filtered->assertSee('Rani P.');
    }

    public function test_buyer_can_complete_and_review_live_jastiper_order(): void
    {
        $jastiper = User::factory()->create([
            'name' => 'Hendra Jastip',
            'role' => 'jastiper',
            'faculty' => 'FT UNNES \'22',
            'origin_city' => 'Solo',
        ]);
        $buyer = User::factory()->create([
            'name' => 'Rina Pembeli',
            'role' => 'pembeli',
        ]);

        $trip = Trip::create([
            'user_id' => $jastiper->id,
            'title' => 'Timlo Solo Sastro',
            'origin' => 'Solo',
            'destination' => 'Sekaran, UNNES',
            'quota' => 10,
            'unit' => 'porsi',
            'price' => 25000,
            'service_fee' => 5000,
            'order_deadline' => now()->addDays(2),
            'return_at' => now()->addDays(3),
            'pickup_point' => 'Depan GSG UNNES',
            'status' => 'open',
        ]);

        $order = Order::create([
            'user_id' => $buyer->id,
            'trip_id' => $trip->id,
            'product_title' => $trip->title,
            'route' => 'Solo → Sekaran, UNNES',
            'pickup_point' => 'Depan GSG UNNES',
            'quantity' => 2,
            'unit_price' => 25000,
            'service_fee' => 5000,
            'total_price' => 60000,
            'payment_method' => 'qris',
            'payment_status' => 'paid',
            'status' => 'ready_for_pickup',
        ]);

        // Cannot review before completed
        $this->actingAs($buyer)
            ->post(route('pesanan.review.store', $order), [
                'product_rating' => 5,
                'store_rating' => 5,
                'comment' => 'Belum selesai',
            ])
            ->assertSessionHasErrors('order');

        // Buyer marks order received
        $this->actingAs($buyer)
            ->patch(route('pesanan.complete', $order))
            ->assertSessionHasNoErrors();

        $this->assertSame('completed', $order->fresh()->status);

        // Buyer submits dual rating (product + store)
        $this->actingAs($buyer)
            ->post(route('pesanan.review.store', $order), [
                'product_rating' => 5,
                'store_rating' => 4,
                'comment' => 'Timlo masih segar dan jastiper tepat waktu di GSG!',
            ])
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'jastiper_id' => $jastiper->id,
            'product_rating' => 5,
            'store_rating' => 4,
        ]);

        // Duplicate review rejected
        $this->actingAs($buyer)
            ->post(route('pesanan.review.store', $order), [
                'product_rating' => 3,
                'store_rating' => 3,
            ])
            ->assertSessionHasErrors('order');

        // Live Jastiper public store profile reflects new rating & review
        $storePage = $this->get(route('jastiper.profile', $jastiper->storeSlug()));
        $storePage->assertOk();
        $storePage->assertSee('Hendra Jastip');
        $storePage->assertSee('4.0');
        $storePage->assertSee('Timlo masih segar dan jastiper tepat waktu di GSG!');

        // Jastiper account /profile shows store reputation & saves store fields
        $this->actingAs($jastiper)
            ->get('/profile')
            ->assertOk()
            ->assertSee('Rating Toko')
            ->assertSee('Lihat Halaman Toko Publik');

        $this->actingAs($jastiper)
            ->patch('/profile', [
                'name' => 'Hendra Jastip',
                'email' => $jastiper->email,
                'faculty' => 'FT UNNES \'22',
                'origin_city' => 'Solo Raya',
                'store_bio' => 'Spesialis kuliner Solo tiap akhir pekan.',
            ])
            ->assertSessionHasNoErrors();

        $this->assertSame('Solo Raya', $jastiper->fresh()->origin_city);
    }
}
