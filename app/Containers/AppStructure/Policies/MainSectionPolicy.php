<?php

namespace App\Containers\AppStructure\Policies;

use App\Containers\User\Models\User;
use App\Containers\AppStructure\Models\MainSection;
use Illuminate\Auth\Access\HandlesAuthorization;

class MainSectionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_main::section');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, MainSection $mainSection): bool
    {
        return $user->can('view_main::section');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_main::section');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, MainSection $mainSection): bool
    {
        return $user->can('update_main::section');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, MainSection $mainSection): bool
    {
        return $user->can('delete_main::section');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_main::section');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, MainSection $mainSection): bool
    {
        return $user->can('force_delete_main::section');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_main::section');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, MainSection $mainSection): bool
    {
        return $user->can('restore_main::section');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_main::section');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, MainSection $mainSection): bool
    {
        return $user->can('replicate_main::section');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_main::section');
    }
}
