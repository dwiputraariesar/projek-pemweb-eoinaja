<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property int $booking_id
 * @property float $amount
 * @property string $provider
 * @property string $status
 * @property array|null $metadata
 */
class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'provider',
        'amount',
        'status',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }
}
