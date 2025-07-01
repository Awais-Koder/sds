<?php

namespace App\Policies;

use App\Models\Submittel;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SubmittelPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_submittel');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Submittel $submittel): bool
    {
        return $user->can('view_submittel');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_submittel');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Submittel $submittel): bool
    {
        return $user->can('update_submittel');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Submittel $submittel): bool
    {
        return $user->can('delete_submittel');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Submittel $submittel): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Submittel $submittel): bool
    {
        return false;
    }
}
