<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Review;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $pembeli = User::updateOrCreate(
            ['email' => 'budi@students.unnes.ac.id'],
            [
                'name' => 'Budi Santoso',
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'faculty' => 'FMIPA UNNES \'23',
                'email_verified_at' => now(),
            ]
        );

        $pembeli2 = User::updateOrCreate(
            ['email' => 'sari@students.unnes.ac.id'],
            [
                'name' => 'Sari Wijaya',
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'faculty' => 'FBS UNNES \'22',
                'email_verified_at' => now(),
            ]
        );

        $jastiper = User::updateOrCreate(
            ['email' => 'rizky@students.unnes.ac.id'],
            [
                'name' => 'Rizky Pratama',
                'password' => Hash::make('password'),
                'role' => 'jastiper',
                'faculty' => 'FT UNNES \'22',
                'origin_city' => 'Solo (Surakarta)',
                'store_bio' => 'Rutin pulang ke Solo dua minggu sekali. Pesanan dibeli langsung di outlet pusat beserta struk asli sebelum berangkat ke Sekaran.',
                'email_verified_at' => now(),
            ]
        );

        $trip = Trip::firstOrCreate(
            [
                'user_id' => $jastiper->id,
                'title' => 'Timlo Solo Sastro & Sosis Basah Notosuman',
            ],
            [
                'origin' => 'Solo',
                'destination' => 'Sekaran, UNNES',
                'description' => 'Dikemas vakum & cooler bag aman sampai titik kumpul GSG UNNES.',
                'quota' => 20,
                'unit' => 'porsi',
                'price' => 32000,
                'service_fee' => 5000,
                'order_deadline' => now()->addDays(3),
                'return_at' => now()->addDays(4),
                'pickup_point' => 'Depan GSG UNNES',
                'status' => 'open',
            ]
        );

        $completedOrder1 = Order::firstOrCreate(
            [
                'user_id' => $pembeli2->id,
                'trip_id' => $trip->id,
                'status' => 'completed',
            ],
            [
                'product_title' => $trip->title,
                'route' => 'Solo → Sekaran, UNNES',
                'pickup_point' => 'Depan GSG UNNES',
                'quantity' => 2,
                'unit_price' => 32000,
                'service_fee' => 5000,
                'total_price' => 74000,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
            ]
        );

        Review::firstOrCreate(
            ['order_id' => $completedOrder1->id],
            [
                'user_id' => $pembeli2->id,
                'jastiper_id' => $jastiper->id,
                'jastiper_slug' => $jastiper->storeSlug(),
                'trip_id' => $trip->id,
                'product_title' => $trip->title,
                'product_rating' => 5,
                'store_rating' => 5,
                'comment' => 'Sosis Solo dan kuah timlonya masih segar banget sampai Sekaran! Kak Rizky ramah dan tepat waktu di depan GSG.',
            ]
        );

        Order::firstOrCreate(
            [
                'user_id' => $pembeli->id,
                'trip_id' => $trip->id,
                'status' => 'ready_for_pickup',
            ],
            [
                'product_title' => $trip->title,
                'route' => 'Solo → Sekaran, UNNES',
                'pickup_point' => 'Depan GSG UNNES',
                'quantity' => 1,
                'unit_price' => 32000,
                'service_fee' => 5000,
                'total_price' => 37000,
                'payment_method' => 'qris',
                'payment_status' => 'paid',
            ]
        );
    }
}
