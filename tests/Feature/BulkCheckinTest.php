<?php

use App\Models\Booking;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Spatie\Permission\Models\Role;

beforeEach(function () {
    Role::firstOrCreate(['name' => 'Administrator']);
    Role::firstOrCreate(['name' => 'Organizer']);
});

test('admin can bulk checkin bookings via ajax and response contains updated ids', function () {
    $admin = User::factory()->create();
    $admin->assignRole('Administrator');

    $org = User::factory()->create();
    $event = Event::create([
        'title' => 'BulkEvent',
        'description' => 'desc',
        'location' => 'loc',
        'date' => now()->addDays(3)->toDateString(),
        'time' => '10:00',
        'price' => 50,
        'quota' => 100,
        'organizer_id' => $org->id,
    ]);

    $ticket = Ticket::create([
        'event_id' => $event->id,
        'name' => 'General',
        'price' => 50,
        'stock' => 100,
    ]);

    $a1 = User::factory()->create();
    $a2 = User::factory()->create();

    $b1 = Booking::create(['user_id' => $a1->id, 'event_id' => $event->id, 'ticket_id' => $ticket->id, 'quantity' => 1, 'status' => 'paid']);
    $b2 = Booking::create(['user_id' => $a2->id, 'event_id' => $event->id, 'ticket_id' => $ticket->id, 'quantity' => 1, 'status' => 'paid']);

    $resp = $this->actingAs($admin)->postJson(route('admin.bookings.bulk_checkin'), ['ids' => [$b1->id, $b2->id]]);
    $resp->assertStatus(200)->assertJson(['updated' => 2]);

    $this->assertDatabaseHas('bookings', ['id' => $b1->id, 'checked_in' => 1]);
    $this->assertDatabaseHas('bookings', ['id' => $b2->id, 'checked_in' => 1]);
});

test('organizer can only bulk checkin their own event bookings', function () {
    $org = User::factory()->create();
    $org->assignRole('Organizer');

    $otherOrg = User::factory()->create();

    $eventMine = Event::create(['title' => 'Mine','description'=>'','location'=>'x','date'=>now()->toDateString(),'time'=>'10:00','price'=>10,'quota'=>10,'organizer_id'=>$org->id]);
    $eventOther = Event::create(['title' => 'Other','description'=>'','location'=>'x','date'=>now()->toDateString(),'time'=>'10:00','price'=>10,'quota'=>10,'organizer_id'=>$otherOrg->id]);

    $ticket1 = Ticket::create(['event_id'=>$eventMine->id,'name'=>'T1','price'=>10,'stock'=>10]);
    $ticket2 = Ticket::create(['event_id'=>$eventOther->id,'name'=>'T2','price'=>10,'stock'=>10]);

    $u1 = User::factory()->create();
    $u2 = User::factory()->create();

    $b1 = Booking::create(['user_id'=>$u1->id,'event_id'=>$eventMine->id,'ticket_id'=>$ticket1->id,'quantity'=>1,'status'=>'paid']);
    $b2 = Booking::create(['user_id'=>$u2->id,'event_id'=>$eventOther->id,'ticket_id'=>$ticket2->id,'quantity'=>1,'status'=>'paid']);

    $resp = $this->actingAs($org)->postJson(route('admin.bookings.bulk_checkin'), ['ids'=>[$b1->id,$b2->id]]);
    $resp->assertStatus(200);
    $data = $resp->json();

    // Organizer should only have updated one booking
    $this->assertEquals(1, $data['updated']);
    $this->assertDatabaseHas('bookings', ['id' => $b1->id, 'checked_in' => 1]);
    $this->assertDatabaseHas('bookings', ['id' => $b2->id, 'checked_in' => 0]);
});
