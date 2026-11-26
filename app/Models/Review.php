<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'order_id',
        'user_id',
        'jastiper_id',
        'jastiper_slug',
        'trip_id',
        'product_id',
        'product_title',
        'product_rating',
        'store_rating',
        'comment',
    ];

    protected function casts(): array
    {
        return [
            'product_rating' => 'integer',
            'store_rating' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jastiper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'jastiper_id');
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }
}
