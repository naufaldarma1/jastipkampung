<?php

namespace App\Http\Controllers;

use App\Support\JastiperRatingService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JastiperProfileController extends Controller
{
    public function show(Request $request, string $slug, JastiperRatingService $ratingService): View
    {
        $store = $ratingService->resolveStoreProfile($slug);
        abort_if(! $store, 404);

        $starFilter = (string) $request->query('star', 'semua');
        $validFilters = ['semua', '5', '4', '3', '2', '1', 'komentar'];
        if (! in_array($starFilter, $validFilters, true)) {
            $starFilter = 'semua';
        }

        $allReviews = collect($store['reviews']);
        $filteredReviews = $allReviews->filter(function (array $review) use ($starFilter) {
            if ($starFilter === 'semua') {
                return true;
            }
            if ($starFilter === 'komentar') {
                return ! empty(trim((string) ($review['comment'] ?? '')));
            }

            return (int) $review['overall_star'] === (int) $starFilter;
        })->values();

        $withCommentCount = $allReviews->filter(fn (array $r) => ! empty(trim((string) ($r['comment'] ?? ''))))->count();

        return view('jastiper.profile', [
            'store' => $store,
            'reviews' => $filteredReviews,
            'starFilter' => $starFilter,
            'withCommentCount' => $withCommentCount,
        ]);
    }
}
