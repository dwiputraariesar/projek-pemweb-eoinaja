<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed roles first (Spatie roles)
        $this->call(RoleSeeder::class);

        // Create a test user (avoid duplicate unique errors)
        $user = User::firstOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            // Use a known password for local testing; factories may randomize otherwise.
            'password' => bcrypt('password'),
        ]);

        // Assign Administrator role if spatie is installed
        if (method_exists($user, 'assignRole')) {
            $user->assignRole('Administrator');
        }
    }
}
