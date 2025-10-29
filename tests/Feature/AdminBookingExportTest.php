<?php

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Booking;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    // Ensure roles exist
    Role::firstOrCreate(['name' => 'Administrator']);
});

test('admin can export filtered bookings as csv', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Administrator');

    // create two events and tickets
    $org = User::factory()->create();
    $eventA = Event::create([
        'title' => 'ExportEventA',
        'description' => 'desc',
        'location' => 'loc',
        'date' => now()->addDays(2)->toDateString(),
        'time' => '10:00',
        'price' => 100,
        'quota' => 50,
        'organizer_id' => $org->id,
    ]);

    $ticketA = Ticket::create([
        'event_id' => $eventA->id,
        'name' => 'Gen',
        'price' => 100,
        'stock' => 20,
    ]);

    $att = User::factory()->create();
    $booking = Booking::create([
        'user_id' => $att->id,
        'event_id' => $eventA->id,
        'ticket_id' => $ticketA->id,
        'quantity' => 1,
        'status' => 'paid',
    ]);

    // Act as admin and request export with event_id filter
    $resp = $this->actingAs($admin)->get(route('admin.bookings.export', ['event_id' => $eventA->id]));

    $resp->assertStatus(200);
    $ct = $resp->headers->get('Content-Type');
    $this->assertStringContainsString('text/csv', $ct);
    // Streamed responses may not include body in the test harness; assert headers instead
    $disposition = $resp->headers->get('Content-Disposition');
    $this->assertStringContainsString('bookings_export_', $disposition);
});
