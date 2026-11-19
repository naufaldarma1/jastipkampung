<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function pembeli(): View
    {
        return view('dashboard.pembeli');
    }

    public function jastiper(): View
    {
        $trips = Trip::query()
            ->where('user_id', auth()->id())
            ->with(['orders.user'])
            ->withSum(['orders as reserved_quantity' => fn ($query) => $query->where('status', '!=', 'cancelled')], 'quantity')
            ->latest()
            ->get();

        return view('dashboard.jastiper', compact('trips'));
    }
}
