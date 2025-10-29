<?php

use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use Illuminate\Support\Str;

it('allows an attendee who booked to submit a review', function () {
    $user = User::factory()->create();

    $event = Event::create([
        'title' => 'Acara Test',
        'description' => 'Deskripsi',
        'location' => 'Jakarta',
        'date' => now()->addDays(1)->toDateString(),
        'time' => now()->format('H:i'),
        'price' => 10000,
        'quota' => 100,
        'organizer_id' => $user->id,
    ]);

    $ticket = \App\Models\Ticket::create([
        'event_id' => $event->id,
        'name' => 'General',
        'price' => 10000,
        'stock' => 100,
    ]);

    Booking::create([
        'user_id' => $user->id,
        'event_id' => $event->id,
        'ticket_id' => $ticket->id,
        'quantity' => 1,
        'status' => 'booked',
    ]);

    $this->actingAs($user)
        ->post(route('events.reviews.store', $event->id), [
            'rating' => 5,
            'comment' => 'Mantap!'
        ])
        ->assertRedirect();

    $this->assertDatabaseHas('reviews', [
        'user_id' => $user->id,
        'event_id' => $event->id,
        'rating' => 5,
        'comment' => 'Mantap!'
    ]);
});

it('prevents a user who did not book from submitting a review', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $event = Event::create([
        'title' => 'Acara Test 2',
        'description' => 'Deskripsi',
        'location' => 'Bandung',
        'date' => now()->addDays(2)->toDateString(),
        'time' => now()->format('H:i'),
        'price' => 20000,
        'quota' => 50,
        'organizer_id' => $user->id,
    ]);

    $ticket = \App\Models\Ticket::create([
        'event_id' => $event->id,
        'name' => 'General',
        'price' => 20000,
        'stock' => 50,
    ]);

    $this->actingAs($other)
        ->post(route('events.reviews.store', $event->id), [
            'rating' => 4,
            'comment' => 'Coba'
        ])
        ->assertSessionHas('error');

    $this->assertDatabaseMissing('reviews', [
        'user_id' => $other->id,
        'event_id' => $event->id,
    ]);
});
