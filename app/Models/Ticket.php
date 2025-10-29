<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Ticket
 *
 * @property int $id
 * @property int $event_id
 * @property string $name
 * @property float $price
 * @property int $stock
 */
class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'price',
        'stock',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }
}
