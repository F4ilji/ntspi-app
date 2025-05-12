<?php

namespace App\Containers\AdditionalEducation\Policies;

use App\Containers\User\Models\User;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdditionalEducationCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_additional::education::category');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AdditionalEducationCategory $additionalEducationCategory): bool
    {
        return $user->can('view_additional::education::category');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_additional::education::category');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AdditionalEducationCategory $additionalEducationCategory): bool
    {
        return $user->can('update_additional::education::category');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AdditionalEducationCategory $additionalEducationCategory): bool
    {
        return $user->can('delete_additional::education::category');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_additional::education::category');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AdditionalEducationCategory $additionalEducationCategory): bool
    {
        return $user->can('force_delete_additional::education::category');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_additional::education::category');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AdditionalEducationCategory $additionalEducationCategory): bool
    {
        return $user->can('restore_additional::education::category');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_additional::education::category');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AdditionalEducationCategory $additionalEducationCategory): bool
    {
        return $user->can('replicate_additional::education::category');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_additional::education::category');
    }
}
