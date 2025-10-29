<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new ticket type for an event.
     */
    public function store(Request $request, Event $event)
    {
        // Only organizer or admin
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
        ]);

        Ticket::create([
            'event_id' => $event->id,
            'name' => $validated['name'],
            'price' => $validated['price'],
            'stock' => $validated['stock'],
        ]);

        return redirect()->back()->with('success', 'Tipe tiket berhasil ditambahkan.');
    }
}
