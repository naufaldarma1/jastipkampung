<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Review;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Support\Collection;

class JastiperRatingService
{
    /**
     * Enrich sample catalog products and live trips with Shopee-style product & store ratings.
     *
     * @param  array<int, array<string, mixed>>  $sampleProducts
     * @return array<int, array<string, mixed>>
     */
    public function enrichSampleProducts(array $sampleProducts): array
    {
        $jastiperConfig = config('catalog.jastipers', []);

        $productReviewStats = Review::query()
            ->whereNotNull('product_id')
            ->selectRaw('product_id, COUNT(*) as review_count, AVG(product_rating) as avg_product_rating')
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        $slugReviewStats = Review::query()
            ->whereNotNull('jastiper_slug')
            ->selectRaw('jastiper_slug, COUNT(*) as review_count, AVG(store_rating) as avg_store_rating')
            ->groupBy('jastiper_slug')
            ->get()
            ->keyBy('jastiper_slug');

        foreach ($sampleProducts as $id => &$item) {
            $slug = $item['jastiper_slug'] ?? 'jastiper-'.$id;
            $baseProdRating = (float) ($item['rating'] ?? 4.8);
            $baseProdCount = (int) ($item['reviews_count'] ?? 10);
            $dbProd = $productReviewStats->get($id);
            $dbProdCount = (int) ($dbProd->review_count ?? 0);
            $dbProdAvg = (float) ($dbProd->avg_product_rating ?? 0);

            $totalProdCount = $baseProdCount + $dbProdCount;
            $combinedProdRating = $totalProdCount > 0
                ? round((($baseProdRating * $baseProdCount) + ($dbProdAvg * $dbProdCount)) / $totalProdCount, 1)
                : $baseProdRating;

            $storeInfo = $jastiperConfig[$slug] ?? [];
            $baseStoreRating = (float) ($storeInfo['store_rating'] ?? $baseProdRating);
            $baseStoreCount = (int) ($storeInfo['store_reviews_count'] ?? $baseProdCount);
            $dbStore = $slugReviewStats->get($slug);
            $dbStoreCount = (int) ($dbStore->review_count ?? 0);
            $dbStoreAvg = (float) ($dbStore->avg_store_rating ?? 0);

            $totalStoreCount = $baseStoreCount + $dbStoreCount;
            $combinedStoreRating = $totalStoreCount > 0
                ? round((($baseStoreRating * $baseStoreCount) + ($dbStoreAvg * $dbStoreCount)) / $totalStoreCount, 1)
                : $baseStoreRating;

            $item['jastiper_slug'] = $slug;
            $item['product_rating'] = $combinedProdRating;
            $item['product_reviews_count'] = $totalProdCount;
            $item['sold_count'] = (int) ($item['sold'] ?? $item['terisi'] ?? 0);
            $item['store_rating'] = $combinedStoreRating;
            $item['store_reviews_count'] = $totalStoreCount;
        }
        unset($item);

        return $sampleProducts;
    }

    /**
     * Build rating metadata for a live Trip collection efficiently.
     *
     * @param  Collection<int, Trip>  $trips
     * @return array{trip_stats: Collection, user_stats: Collection}
     */
    public function statsForTrips(Collection $trips): array
    {
        $tripIds = $trips->pluck('id')->all();
        $userIds = $trips->pluck('user_id')->unique()->all();

        $tripStats = empty($tripIds) ? collect() : Review::query()
            ->whereIn('trip_id', $tripIds)
            ->selectRaw('trip_id, COUNT(*) as review_count, AVG(product_rating) as avg_product_rating')
            ->groupBy('trip_id')
            ->get()
            ->keyBy('trip_id');

        $userStats = empty($userIds) ? collect() : Review::query()
            ->whereIn('jastiper_id', $userIds)
            ->selectRaw('jastiper_id, COUNT(*) as review_count, AVG(store_rating) as avg_store_rating, AVG(product_rating) as avg_product_rating')
            ->groupBy('jastiper_id')
            ->get()
            ->keyBy('jastiper_id');

        return [
            'trip_stats' => $tripStats,
            'user_stats' => $userStats,
        ];
    }

    /**
     * Resolve full Shopee-style Store Profile (either 'u-{userId}' or sample catalog slug).
     *
     * @return array<string, mixed>|null
     */
    public function resolveStoreProfile(string $slug): ?array
    {
        if (str_starts_with($slug, 'u-') && ctype_digit(substr($slug, 2))) {
            $userId = (int) substr($slug, 2);
            $user = User::find($userId);

            if (! $user) {
                return null;
            }

            return $this->buildLiveUserStoreProfile($user);
        }

        $jastiperConfig = config('catalog.jastipers', []);
        if (! isset($jastiperConfig[$slug])) {
            return null;
        }

        return $this->buildSampleStoreProfile($slug, $jastiperConfig[$slug]);
    }

    /**
     * Build Store Profile summary for a real registered User (Jastiper).
     *
     * @return array<string, mixed>
     */
    public function buildLiveUserStoreProfile(User $user): array
    {
        $slug = $user->storeSlug();

        $dbReviews = Review::query()
            ->with('reviewer')
            ->where('jastiper_id', $user->id)
            ->latest()
            ->get();

        $totalReviews = $dbReviews->count();
        $storeRating = $totalReviews > 0 ? round((float) $dbReviews->avg('store_rating'), 1) : null;
        $productRating = $totalReviews > 0 ? round((float) $dbReviews->avg('product_rating'), 1) : null;

        $breakdownCounts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($dbReviews as $rev) {
            $star = max(1, min(5, (int) $rev->product_rating));
            $breakdownCounts[$star]++;
        }

        $trips = Trip::query()
            ->where('user_id', $user->id)
            ->withSum(['orders as reserved_quantity' => fn ($q) => $q->where('status', '!=', 'cancelled')], 'quantity')
            ->latest()
            ->get();

        $tripStats = $this->statsForTrips($trips)['trip_stats'];

        $completedOrders = Order::query()
            ->whereIn('trip_id', $trips->pluck('id'))
            ->where('status', 'completed')
            ->sum('quantity');

        $products = $trips
            ->where('status', 'open')
            ->where('order_deadline', '>', now())
            ->values()
            ->map(function (Trip $trip) use ($user, $slug, $storeRating, $totalReviews, $tripStats) {
                $reserved = (int) ($trip->reserved_quantity ?? 0);
                $tStat = $tripStats->get($trip->id);
                $pCount = (int) ($tStat->review_count ?? 0);
                $pRating = $pCount > 0 ? round((float) $tStat->avg_product_rating, 1) : null;

                return [
                    'id' => 'trip-'.$trip->id,
                    'trip_id' => $trip->id,
                    'type' => 'trip',
                    'rute' => $trip->origin.' → '.$trip->destination,
                    'slot' => max(0, $trip->quota - $reserved),
                    'title' => $trip->title,
                    'jastiper' => $user->name,
                    'jastiper_slug' => $slug,
                    'fakultas' => $user->faculty ?: 'Jastiper Kampung UNNES',
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
                    'product_rating' => $pRating,
                    'product_reviews_count' => $pCount,
                    'sold_count' => $reserved,
                    'store_rating' => $storeRating,
                    'store_reviews_count' => $totalReviews,
                ];
            })
            ->all();

        $reviews = $dbReviews->map(fn (Review $rev) => [
            'buyer_name' => $rev->reviewer?->name ?? 'Mahasiswa UNNES',
            'buyer_faculty' => $rev->reviewer?->faculty ?: 'Pembeli Terverifikasi',
            'product_title' => $rev->product_title,
            'product_rating' => $rev->product_rating,
            'store_rating' => $rev->store_rating,
            'overall_star' => max(1, min(5, (int) $rev->product_rating)),
            'comment' => $rev->comment,
            'date' => $rev->created_at->diffForHumans(),
        ])->all();

        return [
            'slug' => $slug,
            'is_live_user' => true,
            'user_id' => $user->id,
            'name' => $user->name,
            'faculty' => $user->faculty ?: 'Mahasiswa UNNES',
            'origin_city' => $user->origin_city ?: ($trips->first()?->origin ?? 'Kampus UNNES Sekaran'),
            'bio' => $user->store_bio ?: 'Mitra Jastiper mahasiswa UNNES. Melayani titipan kuliner daerah dengan serah terima aman di area kampus Sekaran.',
            'joined_label' => $user->created_at ? $user->created_at->diffForHumans() : 'Baru bergabung',
            'on_time_rate' => $completedOrders > 0 ? '100%' : 'Baru',
            'response_rate' => 'Aktif',
            'store_rating' => $storeRating,
            'product_rating' => $productRating,
            'total_reviews' => $totalReviews,
            'completed_orders' => (int) $completedOrders,
            'active_products_count' => count($products),
            'star_breakdown' => $this->formatBreakdown($breakdownCounts, $totalReviews),
            'products' => $products,
            'reviews' => $reviews,
        ];
    }

    /**
     * Build Store Profile for sample catalog Jastipers merged with real DB reviews.
     *
     * @param  array<string, mixed>  $info
     * @return array<string, mixed>
     */
    protected function buildSampleStoreProfile(string $slug, array $info): array
    {
        $allCatalog = $this->enrichSampleProducts(config('catalog.products', []));
        $reservedByProduct = Order::query()
            ->selectRaw('product_id, SUM(quantity) as reserved_quantity')
            ->whereNull('trip_id')
            ->where('status', '!=', 'cancelled')
            ->groupBy('product_id')
            ->pluck('reserved_quantity', 'product_id');

        $products = [];
        foreach ($allCatalog as $id => $item) {
            if (($item['jastiper_slug'] ?? '') !== $slug) {
                continue;
            }
            $item['id'] = $id;
            $item['type'] = 'sample';
            $item['terisi'] += (int) ($reservedByProduct[$id] ?? 0);
            $item['slot'] = max(0, $item['total'] - $item['terisi']);
            $products[] = $item;
        }

        $dbReviews = Review::query()
            ->with('reviewer')
            ->where('jastiper_slug', $slug)
            ->latest()
            ->get();

        $baseStoreRating = (float) ($info['store_rating'] ?? 4.9);
        $baseStoreCount = (int) ($info['store_reviews_count'] ?? 20);
        $dbCount = $dbReviews->count();

        $totalReviews = $baseStoreCount + $dbCount;
        $storeRating = $totalReviews > 0
            ? round((($baseStoreRating * $baseStoreCount) + ((float) $dbReviews->sum('store_rating'))) / $totalReviews, 1)
            : $baseStoreRating;

        $avgProdBase = count($products) > 0 ? collect($products)->avg('rating') : $baseStoreRating;
        $productRating = $totalReviews > 0
            ? round((($avgProdBase * $baseStoreCount) + ((float) $dbReviews->sum('product_rating'))) / $totalReviews, 1)
            : round((float) $avgProdBase, 1);

        $breakdownCounts = [
            5 => (int) ($info['breakdown'][5] ?? $baseStoreCount),
            4 => (int) ($info['breakdown'][4] ?? 0),
            3 => (int) ($info['breakdown'][3] ?? 0),
            2 => (int) ($info['breakdown'][2] ?? 0),
            1 => (int) ($info['breakdown'][1] ?? 0),
        ];

        foreach ($dbReviews as $rev) {
            $star = max(1, min(5, (int) $rev->product_rating));
            $breakdownCounts[$star]++;
        }

        $liveReviewsFormatted = $dbReviews->map(fn (Review $rev) => [
            'buyer_name' => $rev->reviewer?->name ?? 'Mahasiswa UNNES',
            'buyer_faculty' => $rev->reviewer?->faculty ?: 'Pembeli Terverifikasi',
            'product_title' => $rev->product_title,
            'product_rating' => $rev->product_rating,
            'store_rating' => $rev->store_rating,
            'overall_star' => max(1, min(5, (int) $rev->product_rating)),
            'comment' => $rev->comment,
            'date' => $rev->created_at->diffForHumans(),
        ])->all();

        $sampleReviewsFormatted = collect($info['sample_reviews'] ?? [])->map(fn (array $rev) => [
            'buyer_name' => $rev['buyer_name'],
            'buyer_faculty' => $rev['buyer_faculty'] ?? 'Mahasiswa UNNES',
            'product_title' => $rev['product_title'],
            'product_rating' => (int) $rev['product_rating'],
            'store_rating' => (int) $rev['store_rating'],
            'overall_star' => max(1, min(5, (int) $rev['product_rating'])),
            'comment' => $rev['comment'],
            'date' => $rev['date'],
        ])->all();

        $completedExtra = Order::query()
            ->whereIn('product_id', collect($products)->pluck('id')->all())
            ->where('status', 'completed')
            ->sum('quantity');

        return [
            'slug' => $slug,
            'is_live_user' => false,
            'user_id' => null,
            'name' => $info['name'],
            'faculty' => $info['faculty'],
            'origin_city' => $info['origin_city'],
            'bio' => $info['bio'],
            'joined_label' => $info['joined'] ?? '6 bulan lalu',
            'on_time_rate' => $info['on_time_rate'] ?? '98%',
            'response_rate' => $info['response_rate'] ?? '97%',
            'store_rating' => $storeRating,
            'product_rating' => $productRating,
            'total_reviews' => $totalReviews,
            'completed_orders' => (int) ($info['completed_orders'] ?? 50) + (int) $completedExtra,
            'active_products_count' => count($products),
            'star_breakdown' => $this->formatBreakdown($breakdownCounts, array_sum($breakdownCounts)),
            'products' => $products,
            'reviews' => array_merge($liveReviewsFormatted, $sampleReviewsFormatted),
        ];
    }

    /**
     * @param  array<int, int>  $counts
     * @return array<int, array{count: int, percent: int}>
     */
    protected function formatBreakdown(array $counts, int $total): array
    {
        $result = [];
        foreach ([5, 4, 3, 2, 1] as $star) {
            $count = (int) ($counts[$star] ?? 0);
            $result[$star] = [
                'count' => $count,
                'percent' => $total > 0 ? (int) round(($count / $total) * 100) : 0,
            ];
        }

        return $result;
    }
}
