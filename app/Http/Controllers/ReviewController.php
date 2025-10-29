<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a newly created review in storage.
     */
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:2000',
        ]);

        $user = $request->user();

        // Allow only users who have a booking for this event (or admins)
        $canReview = $user->hasRole('Administrator') || 
            $user->bookings()->where('event_id', $event->id)->exists();

        if (! $canReview) {
            return redirect()->back()->with('error', 'Hanya peserta yang membeli tiket untuk event ini yang boleh memberi review.');
        }

        // One review per user per event
        $existing = Review::where('user_id', $user->id)->where('event_id', $event->id)->first();
        if ($existing) {
            return redirect()->back()->with('error', 'Anda sudah memberikan review untuk event ini.');
        }

        Review::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return redirect()->back()->with('success', 'Terima kasih, review Anda telah disimpan.');
    }

    /**
     * Remove the specified review.
     */
    public function destroy(Review $review)
    {
        $user = Auth::user();

        // Allow owner or admin to delete
        if ($user->id !== $review->user_id && ! $user->hasRole('Administrator')) {
            abort(403);
        }

        $review->delete();

        return redirect()->back()->with('success', 'Review berhasil dihapus.');
    }
}
