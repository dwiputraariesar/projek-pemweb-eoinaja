<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    // Method lain tidak diubah...
    public function index()
    {
        $events = Event::latest()->get();
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
    }

    // --- PERHATIKAN PERUBAHAN DI SINI ---
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

        // KITA NONAKTIFKAN CARA LAMA (MASS ASSIGNMENT)
        // Event::create($validated);

        // KITA GUNAKAN CARA MANUAL UNTUK BYPASS MASS ASSIGNMENT
        $event = new Event;
        $event->title = $validated['title'];
        $event->description = $validated['description'];
        $event->location = $validated['location'];
        $event->date = $validated['date'];
        $event->time = $validated['time'];
        $event->price = $validated['price'];
        $event->quota = $validated['quota'];
        $event->save(); // Simpan data ke database

        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dibuat dengan metode debug!');
    }
    // --- AKHIR DARI PERUBAHAN ---

    public function show(Event $event)
    {
        return view('events.show', compact('event'));
    }

    public function edit(Event $event)
    {
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
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
        $event->update($validated);
        return redirect()->route('events.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    public function destroy(Event $event)
    {
        $event->delete();
        return redirect()->route('events.index')
            ->with('success', 'Event berhasil dihapus!');
    }
}

