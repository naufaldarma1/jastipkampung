<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\JastiperController;
use App\Http\Controllers\JastiperProfileController;
use App\Http\Controllers\KatalogController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\TripOrderController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home')->name('home');

Route::get('/jadi-jastiper', [JastiperController::class, 'show'])->name('jastiper.join');
Route::get('/jastiper/{slug}', [JastiperProfileController::class, 'show'])->name('jastiper.profile');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'redirect'])->name('dashboard');
    Route::get('/katalog', [KatalogController::class, 'index'])->name('katalog');

    Route::middleware('role:pembeli')->group(function () {
        Route::get('/dashboard-pembeli', [DashboardController::class, 'pembeli'])->name('dashboard.pembeli');

        Route::post('/jadi-jastiper', [JastiperController::class, 'store'])->name('jastiper.join.store');

        Route::post('/keranjang/items', [CartController::class, 'add'])->name('cart.add');
        Route::get('/keranjang', [CartController::class, 'index'])->name('cart.index');
        Route::patch('/keranjang/{key}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/keranjang/{key}', [CartController::class, 'remove'])->name('cart.remove');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('checkout.index');
        Route::post('/checkout', [CartController::class, 'placeOrder'])->name('checkout.store');
        Route::get('/pembayaran/{reference}', [PaymentController::class, 'show'])->name('payment.show');
        Route::post('/pembayaran/{reference}/konfirmasi', [PaymentController::class, 'confirm'])->name('payment.confirm');
        Route::get('/pembayaran/{reference}/struk', [PaymentController::class, 'receipt'])->name('payment.receipt');
    });

    Route::get('/pesanan', [OrderController::class, 'index'])->name('pesanan.index');
    Route::post('/katalog/{product}/pesan', [OrderController::class, 'store'])->whereNumber('product')->name('pesanan.store');
    Route::post('/rute/{trip}/pesan', [TripOrderController::class, 'store'])->name('trips.order.store');
    Route::patch('/pesanan/{order}/batal', [OrderController::class, 'cancel'])->name('pesanan.cancel');
    Route::patch('/pesanan/{order}/selesai', [OrderController::class, 'complete'])->name('pesanan.complete');
    Route::post('/pesanan/{order}/ulasan', [ReviewController::class, 'store'])->name('pesanan.review.store');

    Route::middleware('role:jastiper')->group(function () {
        Route::get('/dashboard-jastiper', [DashboardController::class, 'jastiper'])->name('dashboard.jastiper');
        Route::get('/dashboard-jastiper/po/baru', [TripController::class, 'create'])->name('jastiper.trips.create');
        Route::post('/dashboard-jastiper/po', [TripController::class, 'store'])->name('jastiper.trips.store');
        Route::patch('/dashboard-jastiper/po/{trip}/status', [TripController::class, 'updateStatus'])->name('jastiper.trips.status');
        Route::patch('/dashboard-jastiper/pesanan/{order}/status', [TripOrderController::class, 'updateStatus'])->name('jastiper.orders.status');
        Route::patch('/dashboard-jastiper/pesanan/{order}/pembayaran', [PaymentController::class, 'verify'])->name('jastiper.orders.payment.verify');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
