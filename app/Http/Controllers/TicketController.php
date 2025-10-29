<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Menampilkan daftar semua tiket yang dimiliki oleh user yang sedang login.
     */
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
                            ->with('event') // Eager load data event untuk efisiensi
                            ->latest()
                            ->get();

        // Nanti kita akan membuat view 'tickets.index' untuk menampilkan ini
        return view('tickets.index', compact('bookings'));
    }

    /**
     * Menampilkan detail satu tiket, termasuk QR code.
     */
    public function show(Booking $booking)
    {
        // PENTING: Pastikan user hanya bisa melihat tiket miliknya sendiri.
        $this->authorize('view', $booking);

        // Nanti kita akan membuat view 'tickets.show'
        return view('tickets.show', compact('booking'));
    }

    /**
     * Membatalkan booking.
     */
    public function destroy(Booking $booking)
    {
        // PENTING: Pastikan user hanya bisa membatalkan tiket miliknya.
        $this->authorize('delete', $booking);

        // Logika bisnis: Tiket tidak bisa dibatalkan jika event sudah lewat.
        if ($booking->event->date < now()) {
            return back()->with('error', 'Tidak dapat membatalkan booking untuk event yang sudah berlalu.');
        }

        // Ubah status booking menjadi 'cancelled'
        $booking->update(['status' => 'cancelled']);

        return redirect()->route('tickets.index')->with('success', 'Booking berhasil dibatalkan.');
    }
}
