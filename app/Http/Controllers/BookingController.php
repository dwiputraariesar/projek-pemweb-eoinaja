<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class BookingController extends Controller
{
    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        $quantity = $request->input('quantity', 1);
        if ($event->remainingQuota() < $quantity) {
        return back()->with('error', 'Maaf, kuota untuk event ini tidak mencukupi.');
    }
        $total = $event->price * $quantity;

        // Simulasikan "pembayaran sukses"
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'event_id' => $event->id,
            'quantity' => $quantity,
            'total_price' => $total,
            'status' => 'paid',
        ]);

        // Generate QR code (berisi kode booking)
        $qrPath = 'qr/' . $booking->id . '.png';
        Storage::put($qrPath, QrCode::format('png')->size(300)->generate('BOOKING-' . $booking->id));
        $booking->update(['qr_code_path' => $qrPath]);

        return redirect()->route('bookings.show', $booking)->with('success', 'Booking berhasil!');
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        return view('bookings.show', compact('booking'));
    }
}
