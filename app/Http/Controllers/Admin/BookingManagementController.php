<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;

// FQCN simplifications applied: using imported model aliases (helps analyzer suggestions)

class BookingManagementController extends Controller
{
    public function __construct()
    {
        // Use auth middleware and a small closure to enforce role checks.
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = $request->user();
            if (! $user || ! method_exists($user, 'hasRole') || ! $user->hasAnyRole(['Administrator', 'Organizer'])) {
                abort(403);
            }
            return $next($request);
        });
    }

    /**
     * List bookings for admin/organizer to manage and check-in.
     */
    public function index()
    {
        // Filters: search, status, checked_in, event_id
        $user = auth()->user();
        $q = Booking::with('event','ticket','user');

        // Organizer should only see their events' bookings
        if (! $user->hasRole('Administrator')) {
            $q->whereHas('event', function($qq) use ($user) {
                $qq->where('organizer_id', $user->id);
            });
        }

        if ($search = request('search')) {
            $q->where(function($w) use ($search) {
                $w->whereHas('user', function($u) use ($search) { $u->where('name','like','%'.$search.'%')->orWhere('email','like','%'.$search.'%'); })
                  ->orWhereHas('event', function($e) use ($search) { $e->where('title','like','%'.$search.'%'); });
            });
        }

        if ($status = request('status')) {
            $q->where('status', $status);
        }

        if (!is_null(request('checked_in'))) {
            $val = request('checked_in') ? 1 : 0;
            $q->where('checked_in', $val);
        }

        if ($eventId = request('event_id')) {
            $q->where('event_id', $eventId);
        }

        // CSV export
        if (request()->routeIs('admin.bookings.export')) {
            return $this->exportCsv($q->get());
        }

        $bookings = $q->latest()->paginate(20)->withQueryString();
        $eventsList = \App\Models\Event::pluck('title','id');
        return view('admin.bookings.index', compact('bookings','eventsList'));
    }

    /**
     * Export given bookings to CSV (streamed)
     */
    protected function exportCsv($bookings)
    {
        $filename = 'bookings_export_'.now()->format('Ymd_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($bookings) {
            $out = fopen('php://output','w');
            fputcsv($out, ['id','event','user','ticket','quantity','status','checked_in','checked_in_at']);
            foreach ($bookings as $b) {
                fputcsv($out, [
                    $b->id,
                    $b->event->title ?? '',
                    $b->user->email ?? '',
                    $b->ticket->name ?? '',
                    $b->quantity,
                    $b->status,
                    $b->checked_in ? '1' : '0',
                    $b->checked_in_at ? $b->checked_in_at->toDateTimeString() : '',
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk check-in bookings (accepts array of booking ids)
     */
    public function bulkCheckin(Request $request)
    {
        $user = auth()->user();
        $ids = $request->input('ids', []);
        if (empty($ids) || ! is_array($ids)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'No bookings selected.', 'updated' => 0], 422);
            }
            return redirect()->back()->with('error', 'No bookings selected.');
        }

        $bookings = Booking::whereIn('id', $ids)->with('event','user')->get();
        $updated = 0;
        $updatedIds = [];
        foreach ($bookings as $b) {
            // skip already checked-in
            if ($b->checked_in) continue;
            // skip if not permitted for organizers
            if (! $user->hasRole('Administrator') && $user->id !== ($b->event->organizer_id ?? null)) continue;
            $b->checked_in = true;
            $b->checked_in_at = now();
            $b->save();
            $updated++;
            $updatedIds[] = $b->id;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => "{$updated} bookings checked in.",
                'updated' => $updated,
                'ids' => $updatedIds,
            ]);
        }

        return redirect()->back()->with('success', "{$updated} bookings checked in.");
    }

    /**
     * Check-in a booking by QR/token; marks checked_in and timestamp.
     */
    public function checkin(Booking $booking)
    {
        $user = auth()->user();

        // Only Administrator or the event organizer can check in
        if (! $user->hasRole('Administrator') && $user->id !== $booking->event->organizer_id) {
            abort(403);
        }

        if ($booking->checked_in) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json(['message' => 'Booking already checked in.', 'id' => $booking->id], 200);
            }
            return redirect()->back()->with('info', 'Booking already checked in.');
        }

        $booking->checked_in = true;
        $booking->checked_in_at = now();
        $booking->save();

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json(['message' => 'Attendee checked in.', 'id' => $booking->id]);
        }

        return redirect()->back()->with('success', 'Attendee checked in.');
    }
}
