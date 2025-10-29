<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any events.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the event.
     */
    public function view(User $user, Event $event): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create events.
     */
    public function create(User $user): bool
    {
        // Allow if user has Organizer or Administrator role (if Spatie installed)
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('Organizer') || $user->hasRole('Administrator');
        }

        // Fallback: allow authenticated users to create
        return true;
    }

    /**
     * Determine whether the user can update the event.
     */
    public function update(User $user, Event $event): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('Administrator')) {
            return true;
        }

        return $user->id === $event->organizer_id;
    }

    /**
     * Determine whether the user can delete the event.
     */
    public function delete(User $user, Event $event): bool
    {
        if (method_exists($user, 'hasRole') && $user->hasRole('Administrator')) {
            return true;
        }

        return $user->id === $event->organizer_id;
    }
}
