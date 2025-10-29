<?php

use App\Models\Event;
use App\Models\User;

test('organizer creating an event is set as organizer_id', function () {
    // Ensure Organizer role exists
    if (class_exists(\Spatie\Permission\Models\Role::class) && \Illuminate\Support\Facades\Schema::hasTable('roles')) {
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Organizer', 'guard_name' => 'web']);
    }

    $organizer = User::factory()->create();
    if (method_exists($organizer, 'assignRole')) {
        $organizer->assignRole('Organizer');
    }

    $this->actingAs($organizer)->post(route('events.store'), [
        'title' => 'Test Event',
        'description' => 'Desc',
        'location' => 'Somewhere',
        'date' => now()->addDays(10)->toDateString(),
        'time' => '10:00',
        'price' => 0,
        'quota' => 100,
    ]);

    $event = Event::where('title', 'Test Event')->first();

    expect($event)->not->toBeNull();
    expect($event->organizer_id)->toBe($organizer->id);
});
