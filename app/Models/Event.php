<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Ticket;

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
     * Relasi ke Booking
     * 1 Event bisa punya banyak Booking
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Cek sisa kuota event
     */
    public function remainingQuota()
    {
        // Menghitung hanya booking yang statusnya 'paid' atau 'success'
        $booked = $this->bookings()->whereIn('status', ['paid', 'success'])->sum('quantity');
        return $this->quota - $booked;
    }

    /**
     * Relasi: Satu event bisa memiliki banyak ulasan.
     * (INI BAGIAN YANG BARU DITAMBAHKAN)
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

