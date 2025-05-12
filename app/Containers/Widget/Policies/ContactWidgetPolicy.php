<?php

namespace App\Containers\Widget\Policies;

use App\Containers\User\Models\User;
use App\Containers\Widget\Models\ContactWidget;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContactWidgetPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_contact::widget');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ContactWidget $contactWidget): bool
    {
        return $user->can('view_contact::widget');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_contact::widget');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ContactWidget $contactWidget): bool
    {
        return $user->can('update_contact::widget');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ContactWidget $contactWidget): bool
    {
        return $user->can('delete_contact::widget');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_contact::widget');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, ContactWidget $contactWidget): bool
    {
        return $user->can('force_delete_contact::widget');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_contact::widget');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, ContactWidget $contactWidget): bool
    {
        return $user->can('restore_contact::widget');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_contact::widget');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, ContactWidget $contactWidget): bool
    {
        return $user->can('replicate_contact::widget');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_contact::widget');
    }
}
