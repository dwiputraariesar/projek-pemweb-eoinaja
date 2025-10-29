<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

it('creates transaction, marks booking paid and generates qr', function () {
    Storage::fake('public');

    $organizer = User::factory()->create();
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Organizer']);
    \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Attendee']);
    $organizer->assignRole('Organizer');

    $event = Event::create([
        'title' => 'Test Event',
        'description' => 'desc',
        'location' => 'loc',
        'date' => now()->toDateString(),
        'time' => now()->toTimeString(),
        'price' => 100,
        'quota' => 100,
        'organizer_id' => $organizer->id,
    ]);

    $ticket = Ticket::create([
        'event_id' => $event->id,
        'name' => 'General',
        'price' => 100,
        'stock' => 10,
    ]);

    $attendee = User::factory()->create();
    $attendee->assignRole('Attendee');

    $this->actingAs($attendee)->post(route('events.book', $event), [
        'ticket_id' => $ticket->id,
        'quantity' => 2,
    ])->assertRedirect(route('bookings.index'));

    $booking = Booking::where('user_id', $attendee->id)->latest()->first();

    // pay
    $this->actingAs($attendee)->post(route('bookings.pay', $booking))->assertRedirect();

    $booking->refresh();

    expect($booking->status)->toBe('paid');
    expect($booking->transaction_id)->not->toBeNull();
    expect($booking->qr_code)->not->toBeNull();

    $this->assertTrue(Storage::disk('public')->exists($booking->qr_code));
});
