<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class KatalogController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'semua');
        $allData = config('catalog.products', []);

        $reservedByProduct = Order::query()
            ->selectRaw('product_id, SUM(quantity) as reserved_quantity')
            ->where('status', '!=', 'cancelled')
            ->groupBy('product_id')
            ->pluck('reserved_quantity', 'product_id');

        foreach ($allData as $id => &$item) {
            $item['id'] = $id;
            $item['type'] = 'sample';
            $item['terisi'] += (int) ($reservedByProduct[$id] ?? 0);
            $item['slot'] = max(0, $item['total'] - $item['terisi']);
        }
        unset($item);

        $liveTrips = Trip::query()
            ->with('jastiper')
            ->withSum(['orders as reserved_quantity' => fn ($query) => $query->where('status', '!=', 'cancelled')], 'quantity')
            ->where('status', 'open')
            ->where('order_deadline', '>', now())
            ->latest()
            ->get()
            ->map(function (Trip $trip) {
                $reserved = (int) ($trip->reserved_quantity ?? 0);

                return [
                    'id' => 'trip-'.$trip->id,
                    'trip_id' => $trip->id,
                    'type' => 'trip',
                    'rute' => $trip->origin.' → '.$trip->destination,
                    'slot' => max(0, $trip->quota - $reserved),
                    'title' => $trip->title,
                    'jastiper' => $trip->jastiper->name,
                    'fakultas' => 'Jastiper Kampung',
                    'terisi' => $reserved,
                    'total' => $trip->quota,
                    'satuan_kuota' => $trip->unit,
                    'countdown' => $trip->order_deadline->diffForHumans(),
                    'kembali' => $trip->return_at->timezone('Asia/Jakarta')->format('D, H:i').' WIB',
                    'kumpul' => $trip->pickup_point,
                    'harga' => $trip->price,
                    'satuan_harga' => $trip->unit,
                    'fee' => $trip->service_fee,
                    'kota' => $trip->origin,
                    'description' => $trip->description,
                ];
            });

        $allData = array_merge($allData, $liveTrips->all());

        $filtered = collect($allData)->filter(function ($item) use ($search, $filter) {
            $matchesCity = $filter === 'semua' || strcasecmp($item['kota'], $filter) === 0;
            $matchesSearch = ! $search
                || stripos($item['title'], $search) !== false
                || stripos($item['kota'], $search) !== false
                || stripos($item['rute'], $search) !== false;

            return $matchesCity && $matchesSearch;
        })->values();

        $perPage = 8;
        $page = LengthAwarePaginator::resolveCurrentPage();
        $products = new LengthAwarePaginator(
            $filtered->forPage($page, $perPage),
            $filtered->count(),
            $perPage,
            $page,
            ['path' => route('katalog'), 'query' => $request->query()]
        );

        return view('katalog.index', [
            'products' => $products,
            'cities' => collect(config('catalog.cities', []))
                ->merge($liveTrips->pluck('kota'))
                ->unique()
                ->sort()
                ->values(),
            'currentFilter' => $filter,
            'search' => $search,
        ]);
    }
}
