<?php

namespace App\Containers\Education\Policies;

use App\Containers\User\Models\User;
use App\Containers\Education\Models\DirectionStudy;
use Illuminate\Auth\Access\HandlesAuthorization;

class DirectionStudyPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_direction::study');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DirectionStudy $directionStudy): bool
    {
        return $user->can('view_direction::study');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_direction::study');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DirectionStudy $directionStudy): bool
    {
        return $user->can('update_direction::study');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DirectionStudy $directionStudy): bool
    {
        return $user->can('delete_direction::study');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_direction::study');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, DirectionStudy $directionStudy): bool
    {
        return $user->can('force_delete_direction::study');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_direction::study');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, DirectionStudy $directionStudy): bool
    {
        return $user->can('restore_direction::study');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_direction::study');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, DirectionStudy $directionStudy): bool
    {
        return $user->can('replicate_direction::study');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_direction::study');
    }
}
