<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VirtualExhibition;
use Illuminate\Auth\Access\HandlesAuthorization;

class VirtualExhibitionPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_virtual::exhibition');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, VirtualExhibition $virtualExhibition): bool
    {
        return $user->can('view_virtual::exhibition');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_virtual::exhibition');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, VirtualExhibition $virtualExhibition): bool
    {
        return $user->can('update_virtual::exhibition');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, VirtualExhibition $virtualExhibition): bool
    {
        return $user->can('delete_virtual::exhibition');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_virtual::exhibition');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, VirtualExhibition $virtualExhibition): bool
    {
        return $user->can('force_delete_virtual::exhibition');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_virtual::exhibition');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, VirtualExhibition $virtualExhibition): bool
    {
        return $user->can('restore_virtual::exhibition');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_virtual::exhibition');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, VirtualExhibition $virtualExhibition): bool
    {
        return $user->can('replicate_virtual::exhibition');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_virtual::exhibition');
    }
}
