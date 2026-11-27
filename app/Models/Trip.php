<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trip extends Model
{
    protected $fillable = [
        'user_id', 'title', 'origin', 'destination', 'description', 'quota',
        'unit', 'price', 'service_fee', 'order_deadline', 'return_at',
        'pickup_point', 'status',
    ];

    protected function casts(): array
    {
        return [
            'order_deadline' => 'datetime',
            'return_at' => 'datetime',
        ];
    }

    public function jastiper(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function countdownLabel(): string
    {
        if (! $this->order_deadline) {
            return '-';
        }

        if ($this->order_deadline->isPast()) {
            return 'Sudah ditutup';
        }

        $diff = $this->order_deadline
            ->copy()
            ->locale('id')
            ->diffForHumans(now(), [
                'parts' => 2,
                'join' => ' ',
                'syntax' => CarbonInterface::DIFF_ABSOLUTE,
            ]);

        return $diff.' lagi';
    }

    public function returnScheduleLabel(): string
    {
        if (! $this->return_at) {
            return '-';
        }

        return $this->return_at
            ->copy()
            ->timezone('Asia/Jakarta')
            ->locale('id')
            ->translatedFormat('l, H:i').' WIB';
    }
}
