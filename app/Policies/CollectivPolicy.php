<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Collectiv;
use Illuminate\Auth\Access\HandlesAuthorization;

class CollectivPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_collectiv');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Collectiv $collectiv): bool
    {
        return $user->can('view_collectiv');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_collectiv');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Collectiv $collectiv): bool
    {
        return $user->can('update_collectiv');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Collectiv $collectiv): bool
    {
        return $user->can('delete_collectiv');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_collectiv');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Collectiv $collectiv): bool
    {
        return $user->can('force_delete_collectiv');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_collectiv');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Collectiv $collectiv): bool
    {
        return $user->can('restore_collectiv');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_collectiv');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Collectiv $collectiv): bool
    {
        return $user->can('replicate_collectiv');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_collectiv');
    }
}
