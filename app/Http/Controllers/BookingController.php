<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Storage;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class BookingController extends Controller
{
    /**
     * List bookings for authenticated user.
     */
    public function index()
    {
        $bookings = auth()->user()->bookings()->with('event','ticket')->latest()->get();
        return view('bookings.index', compact('bookings'));
    }

    /**
     * Store a new booking for an event's ticket.
     */
    public function store(Request $request, Event $event)
    {
        /** @var Event $event */
        $validated = $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $ticket = Ticket::findOrFail($validated['ticket_id']);

        // Check stock
        if ($ticket->stock < $validated['quantity']) {
            return back()->withErrors(['quantity' => 'Not enough tickets available.']);
        }

        // Create booking
        $booking = Booking::create([
            'user_id' => auth()->id(),
            'event_id' => $event->getKey(),
            'ticket_id' => $ticket->id,
            'quantity' => $validated['quantity'],
            'status' => 'pending',
        ]);

        // Decrement ticket stock
        $ticket->decrement('stock', $validated['quantity']);

        return redirect()->route('bookings.index')->with('success', 'Booking created.');
    }

    /**
     * Show a booking.
     */
    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        /** @var Booking $booking */
        return view('bookings.show', compact('booking'));
    }

    /**
     * Simulate payment for a booking (mock gateway).
     */
    public function pay(Request $request, Booking $booking)
    {
        /** @var Booking $booking */
        /** @var User $user */
        // Only owner can pay
        if (auth()->id() !== $booking->user_id) {
            abort(403);
        }

        if ($booking->status === 'paid') {
            return back()->with('info', 'Booking already paid.');
        }

    $amount = $booking->ticket->price * $booking->getAttribute('quantity');

        $transaction = Transaction::create([
            'booking_id' => $booking->id,
            'provider' => 'mock',
            'amount' => $amount,
            'status' => 'success',
            'metadata' => ['method' => 'mock_payment'],
        ]);

        // mark booking as paid
        $booking->transaction_id = $transaction->id;
        $booking->status = 'paid';

        // generate QR (SVG) and store to public disk
        $payload = json_encode([
            'booking_id' => $booking->getKey(),
            'user_id' => $booking->getAttribute('user_id'),
            'transaction_id' => $transaction->id,
        ]);

        $options = new QROptions([
            'outputType' => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel' => QRCode::ECC_L,
        ]);

        $svg = (new QRCode($options))->render($payload);

    $path = 'qrcodes/booking_'.$booking->getKey().'.svg';
        Storage::disk('public')->put($path, $svg);

        $booking->qr_code = $path;
        $booking->save();

        return redirect()->route('bookings.show', $booking)->with('success', 'Payment successful. Ticket confirmed.');
    }

    /**
     * Download generated ticket PDF if available.
     */
    public function downloadTicket(Booking $booking)
    {
        $this->authorize('view', $booking);

        $path = 'public/tickets/booking-' . $booking->getKey() . '.pdf';
        if (Storage::exists($path)) {
            return response()->download(storage_path('app/' . $path));
        }

        abort(404, 'Ticket PDF not found.');
    }

    /**
     * Cancel a booking (if allowed).
     */
    public function destroy(Booking $booking)
    {
        /** @var Booking $booking */
        $this->authorize('delete', $booking);

        // restore stock
    $booking->ticket->increment('stock', $booking->getAttribute('quantity'));
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('bookings.index')->with('success', 'Booking cancelled.');
    }
}