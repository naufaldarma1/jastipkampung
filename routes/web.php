<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::middleware('role:pembeli')->group(function () {
        Route::get('/dashboard-pembeli', function () {
            return view('dashboard-pembeli');
        })->name('dashboard.pembeli');
    });

    Route::middleware('role:jastiper')->group(function () {
        Route::get('/dashboard-jastiper', function () {
            return view('dashboard-jastiper');
        })->name('dashboard.jastiper');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
