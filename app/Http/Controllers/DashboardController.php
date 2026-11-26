<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Support\JastiperRatingService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return redirect()->route(
            $request->user()->role === 'jastiper' ? 'dashboard.jastiper' : 'dashboard.pembeli'
        );
    }

    public function pembeli(): View
    {
        return view('dashboard.pembeli');
    }

    public function jastiper(JastiperRatingService $ratingService): View
    {
        $user = auth()->user();

        $trips = Trip::query()
            ->where('user_id', $user->id)
            ->with(['orders.user', 'orders.review'])
            ->withSum(['orders as reserved_quantity' => fn ($query) => $query->where('status', '!=', 'cancelled')], 'quantity')
            ->latest()
            ->get();

        $storeProfile = $ratingService->buildLiveUserStoreProfile($user);

        return view('dashboard.jastiper', compact('trips', 'storeProfile'));
    }
}
