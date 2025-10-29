<?php

use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    // Ensure Attendee role exists for Spatie assignRole during registration
    if (class_exists(Role::class) && Schema::hasTable('roles')) {
        Role::firstOrCreate(['name' => 'Attendee', 'guard_name' => 'web']);
    }

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'Attendee',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});
