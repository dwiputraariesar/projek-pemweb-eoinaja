<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'rating',
        'comment',
    ];

    /**
     * Relasi: Ulasan ini milik satu User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Ulasan ini untuk satu Event.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    
    /**
     * Relasi: Satu user bisa memiliki banyak ulasan.
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}