<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
    ];

    /**
     * Get the bookings for the event.
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
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

