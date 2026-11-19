<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JastiperController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->user()) {
            return redirect()->route('register', ['role' => 'jastiper']);
        }

        if ($request->user()->role === 'jastiper') {
            return redirect()->route('dashboard.jastiper');
        }

        return view('jastiper.join');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->role === 'pembeli', 403);

        $request->user()->update(['role' => 'jastiper']);

        return redirect()->route('dashboard.jastiper')
            ->with('status', 'Akun Anda sekarang terdaftar sebagai Jastiper.');
    }
}
