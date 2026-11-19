<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TripController extends Controller
{
    public function create(): View
    {
        return view('trips.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:120'],
            'origin' => ['required', 'string', 'max:80'],
            'destination' => ['required', 'string', 'max:80'],
            'description' => ['nullable', 'string', 'max:1000'],
            'quota' => ['required', 'integer', 'min:1', 'max:1000'],
            'unit' => ['required', 'string', 'max:30'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'service_fee' => ['required', 'integer', 'min:0', 'max:10000000'],
            'order_deadline' => ['required', 'date', 'after:now'],
            'return_at' => ['required', 'date', 'after:order_deadline'],
            'pickup_point' => ['required', 'string', 'max:150'],
        ]);

        $request->user()->trips()->create($validated + ['status' => 'open']);

        return redirect()->route('dashboard.jastiper')->with('status', 'PO berhasil dipublikasikan dan sekarang tampil di katalog.');
    }

    public function updateStatus(Request $request, Trip $trip): RedirectResponse
    {
        abort_unless($trip->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'status' => ['required', 'in:open,closed'],
        ]);

        if ($validated['status'] === 'open' && $trip->order_deadline->isPast()) {
            return back()->withErrors(['status' => 'PO yang sudah melewati batas waktu tidak dapat dibuka kembali.']);
        }

        $trip->update(['status' => $validated['status']]);

        return back()->with('status', $validated['status'] === 'open' ? 'PO dibuka kembali.' : 'PO ditutup dari katalog.');
    }
}
