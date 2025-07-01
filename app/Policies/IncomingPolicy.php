<?php

namespace App\Policies;

use App\Models\Incoming;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IncomingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_incoming');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Incoming $incmoming): bool
    {
        return $user->can('view_incoming');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_incoming');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Incoming $incmoming): bool
    {
        return $user->can('update_incoming');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Incoming $incmoming): bool
    {
        return $user->can('delete_incoming');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Incoming $incmoming): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Incoming $incmoming): bool
    {
        return false;
    }
}
