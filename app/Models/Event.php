<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

/**
 * App\Models\Event
 *
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property string $location
 * @property string $date
 * @property string $time
 * @property float $price
 * @property int $quota
 * @property int|null $organizer_id
 */

class Event extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'location',
        'date',
        'time',
        'price',
        'quota',
        'organizer_id',
    ];

    /**
     * Get the bookings for the event.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Ticket types for this event.
     */
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Reviews left for the event.
     */
    public function reviews()
    {
        return $this->hasMany(\App\Models\Review::class);
    }

    /**
     * The organizer (owner) of the event.
     */
    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    /**
     * Calculate the remaining quota for the event.
     */
    public function remainingQuota()
    {
        // Menghitung jumlah tiket yang sudah di-booking
        $booked = $this->bookings()->sum('quantity');
        return $this->quota - $booked;
    }
}

