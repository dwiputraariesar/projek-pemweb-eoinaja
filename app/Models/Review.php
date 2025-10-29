<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Review
 *
 * @property int $id
 * @property int $user_id
 * @property int $event_id
 * @property int $rating
 * @property string|null $comment
 */
class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'event_id',
        'rating',
        'comment',
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
