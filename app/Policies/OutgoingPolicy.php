<?php

namespace App\Policies;

use App\Models\Outgoing;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class OutgoingPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_outgoing');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Outgoing $outgoing): bool
    {
        return $user->can('view_outgoing');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_outgoing');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Outgoing $outgoing): bool
    {
        return $user->can('update_outgoing');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Outgoing $outgoing): bool
    {
        return $user->can('delete_outgoing');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Outgoing $outgoing): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Outgoing $outgoing): bool
    {
        return false;
    }
}
