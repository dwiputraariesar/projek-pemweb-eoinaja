<?php

use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

test('organizer can manage own event', function () {
    // Ensure Organizer role exists for tests
    if (class_exists(Role::class) && Schema::hasTable('roles')) {
        Role::firstOrCreate(['name' => 'Organizer', 'guard_name' => 'web']);
    }

    $organizer = User::factory()->create();

    if (method_exists($organizer, 'assignRole')) {
        $organizer->assignRole('Organizer');
    }

    $event = new Event(['organizer_id' => $organizer->id]);

    expect(Gate::forUser($organizer)->allows('update', $event))->toBeTrue();
    expect(Gate::forUser($organizer)->allows('delete', $event))->toBeTrue();
});

test('other user cannot manage event', function () {
    $organizer = User::factory()->create();
    $other = User::factory()->create();

    if (class_exists(Role::class) && Schema::hasTable('roles')) {
        Role::firstOrCreate(['name' => 'Organizer', 'guard_name' => 'web']);
    }

    if (method_exists($organizer, 'assignRole')) {
        $organizer->assignRole('Organizer');
    }

    $event = new Event(['organizer_id' => $organizer->id]);

    expect(Gate::forUser($other)->allows('update', $event))->toBeFalse();
    expect(Gate::forUser($other)->allows('delete', $event))->toBeFalse();
});
