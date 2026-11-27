<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Trip;
use App\Support\JastiperRatingService;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class KatalogController extends Controller
{
    public function index(Request $request, JastiperRatingService $ratingService)
    {
        $search = $request->input('search');
        $filter = $request->input('filter', 'semua');
        $sort = $request->input('sort', 'rekomendasi');
        $allData = $ratingService->enrichSampleProducts(config('catalog.products', []));

        $reservedByProduct = Order::query()
            ->selectRaw('product_id, SUM(quantity) as reserved_quantity')
            ->whereNull('trip_id')
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

        $rawTrips = Trip::query()
            ->with('jastiper')
            ->withSum(['orders as reserved_quantity' => fn ($query) => $query->where('status', '!=', 'cancelled')], 'quantity')
            ->where('status', 'open')
            ->where('order_deadline', '>', now())
            ->latest()
            ->get();

        $liveStats = $ratingService->statsForTrips($rawTrips);
        $tripStats = $liveStats['trip_stats'];
        $userStats = $liveStats['user_stats'];

        $liveTrips = $rawTrips->map(function (Trip $trip) use ($tripStats, $userStats) {
            $reserved = (int) ($trip->reserved_quantity ?? 0);
            $tStat = $tripStats->get($trip->id);
            $uStat = $userStats->get($trip->user_id);

            $pCount = (int) ($tStat->review_count ?? 0);
            $pRating = $pCount > 0 ? round((float) $tStat->avg_product_rating, 1) : null;

            $sCount = (int) ($uStat->review_count ?? 0);
            $sRating = $sCount > 0 ? round((float) $uStat->avg_store_rating, 1) : null;

            return [
                'id' => 'trip-'.$trip->id,
                'trip_id' => $trip->id,
                'type' => 'trip',
                'rute' => $trip->origin.' → '.$trip->destination,
                'slot' => max(0, $trip->quota - $reserved),
                'title' => $trip->title,
                'jastiper' => $trip->jastiper->name,
                'jastiper_slug' => $trip->jastiper->storeSlug(),
                'fakultas' => $trip->jastiper->faculty ?: 'Jastiper Kampung',
                'terisi' => $reserved,
                'total' => $trip->quota,
                'satuan_kuota' => $trip->unit,
                'countdown' => $trip->countdownLabel(),
                'kembali' => $trip->returnScheduleLabel(),
                'kumpul' => $trip->pickup_point,
                'harga' => $trip->price,
                'satuan_harga' => $trip->unit,
                'fee' => $trip->service_fee,
                'kota' => $trip->origin,
                'description' => $trip->description,
                'product_rating' => $pRating,
                'product_reviews_count' => $pCount,
                'sold_count' => $reserved,
                'store_rating' => $sRating,
                'store_reviews_count' => $sCount,
            ];
        });

        $allData = array_merge($allData, $liveTrips->all());

        $filtered = collect($allData)->filter(function ($item) use ($search, $filter) {
            $matchesCity = $filter === 'semua' || strcasecmp($item['kota'], $filter) === 0;
            $matchesSearch = ! $search
                || stripos($item['title'], $search) !== false
                || stripos($item['kota'], $search) !== false
                || stripos($item['rute'], $search) !== false
                || stripos($item['jastiper'], $search) !== false;

            return $matchesCity && $matchesSearch;
        });

        if ($sort === 'rating') {
            $filtered = $filtered->sortByDesc(fn ($item) => [
                $item['product_rating'] ?? 0,
                $item['product_reviews_count'] ?? 0,
            ]);
        } elseif ($sort === 'terlaris') {
            $filtered = $filtered->sortByDesc(fn ($item) => $item['sold_count'] ?? 0);
        }

        $filtered = $filtered->values();

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
            'currentSort' => $sort,
            'search' => $search,
        ]);
    }
}
