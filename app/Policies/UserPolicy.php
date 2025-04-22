<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $authUser): bool
    {

        return $authUser->can('manage-users');
    }


    /**
     * Determine whether the user can view the model.
     */
    public function view(User $authUser, User $user): bool
    {

        return $user->can('manage-users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('manage-users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $authUser, User $model): bool
    {
        // Superadmin bisa update semua user
        if ($authUser->hasRole('superadmin')) {
            return true;
        }

        // Foundation hanya bisa update user dengan foundation_id yang sama
        if ($authUser->hasRole('foundation') && $authUser->foundation_id == $model->foundation_id) {
            return true;
        }

        // Selain itu, tidak boleh update
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $authUser, User $model): bool
    {
        return $authUser->hasRole('superadmin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $authUser, User $model): bool
    {
        return $authUser->hasRole('superadmin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $authUser, User $model): bool
    {
        return $authUser->hasRole('superadmin');
    }
}
