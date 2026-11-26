<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'trip_id', 'product_id', 'product_title', 'route', 'pickup_point',
        'quantity', 'unit_price', 'service_fee', 'total_price', 'payment_method',
        'payment_status', 'checkout_reference', 'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(Trip::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
