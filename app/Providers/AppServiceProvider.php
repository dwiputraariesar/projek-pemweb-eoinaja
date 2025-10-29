<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Middlewares\RoleMiddleware;
use Spatie\Permission\Models\Role;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register Spatie role middleware alias if the class exists.
        if (class_exists(RoleMiddleware::class)) {
            $this->app->make(Router::class)->aliasMiddleware('role', RoleMiddleware::class);
        }

        // Ensure default roles exist (development/test convenience).
        // This only runs when the roles table exists and Spatie's Role model is available.
        if (class_exists(Role::class) && Schema::hasTable('roles')) {
            $defaultRoles = ['Attendee', 'Organizer', 'Administrator'];
            foreach ($defaultRoles as $r) {
                Role::firstOrCreate(['name' => $r, 'guard_name' => 'web']);
            }
        }
    }
}
