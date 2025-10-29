<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::latest()->get();
        return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Event::class);

        return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
        ]);

    // Authorize creation
    $this->authorize('create', Event::class);

    // Menggunakan cara manual untuk bypass Mass Assignment
    $event = new Event;
        $event->title = $validated['title'];
        $event->description = $validated['description'];
        $event->location = $validated['location'];
        $event->date = $validated['date'];
        $event->time = $validated['time'];
        $event->price = $validated['price'];
        $event->quota = $validated['quota'];
        // set organizer to current user when available
        if (auth()->check()) {
            $event->organizer_id = auth()->id();
        }
        $event->save(); // Simpan data ke database

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dibuat!');
    }
    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
    // eager load reviews with user and tickets
    /** @var \App\Models\Event $event */
    $event->load(['reviews.user', 'tickets']);

    $averageRating = $event->reviews()->avg('rating');

        $canReview = false;
        if (auth()->check()) {
            $user = auth()->user();
            /** @var \App\Models\User $user */
            $canReview = $user->hasRole('Administrator') || $user->bookings()->where('event_id', $event->getKey())->exists();
        }

        return view('events.show', compact('event', 'averageRating', 'canReview'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        return view('events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
        ]);

        // --- PERUBAHAN UTAMA ADA DI SINI ---
        // Kita ganti $event->update($validated); dengan cara manual

        $event->title = $validated['title'];
        $event->description = $validated['description'];
        $event->location = $validated['location'];
        $event->date = $validated['date'];
        $event->time = $validated['time'];
        $event->price = $validated['price'];
        $event->quota = $validated['quota'];
        $event->save(); // Simpan perubahan ke database
        // --- AKHIR DARI PERUBAHAN ---

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dihapus!');
    }
}

