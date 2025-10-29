<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;

test('attendee can book a ticket and stock decreases', function () {
    // Create an event and ticket
    $organizer = User::factory()->create();
    $event = Event::create([
        'title' => 'E1',
        'description' => 'desc',
        'location' => 'loc',
        'date' => now()->addDays(5)->toDateString(),
        'time' => '10:00',
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

    $this->actingAs($attendee)->post(route('events.book', $event), [
        'ticket_id' => $ticket->id,
        'quantity' => 2,
    ])->assertRedirect(route('bookings.index'));

    $this->assertDatabaseHas('bookings', [
        'user_id' => $attendee->id,
        'event_id' => $event->id,
        'ticket_id' => $ticket->id,
        'quantity' => 2,
    ]);

    $this->assertDatabaseHas('tickets', [
        'id' => $ticket->id,
        'stock' => 8,
    ]);
});
