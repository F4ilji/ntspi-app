<?php

namespace App\Containers\Schedule\Policies;

use App\Containers\User\Models\User;
use App\Containers\Schedule\Models\EducationalGroup;
use Illuminate\Auth\Access\HandlesAuthorization;

class EducationalGroupPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_educational::group');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EducationalGroup $educationalGroup): bool
    {
        return $user->can('view_educational::group');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_educational::group');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EducationalGroup $educationalGroup): bool
    {
        return $user->can('update_educational::group');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EducationalGroup $educationalGroup): bool
    {
        return $user->can('delete_educational::group');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_educational::group');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, EducationalGroup $educationalGroup): bool
    {
        return $user->can('force_delete_educational::group');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_educational::group');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, EducationalGroup $educationalGroup): bool
    {
        return $user->can('restore_educational::group');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_educational::group');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, EducationalGroup $educationalGroup): bool
    {
        return $user->can('replicate_educational::group');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_educational::group');
    }
}
