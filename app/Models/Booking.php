<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Booking
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int|null $ticket_id
 * @property int $quantity
 * @property string $status
 * @property bool $checked_in
 * @property \Illuminate\Support\Carbon|null $checked_in_at
 * @property string|null $qr_code
 * @property int|null $transaction_id
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'quantity',
        'total_price',
        'status',
        'qr_code_path',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}