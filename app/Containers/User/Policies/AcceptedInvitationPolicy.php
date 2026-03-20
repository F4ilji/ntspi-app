<?php

namespace App\Containers\User\Policies;

use App\Containers\User\Models\User;
use App\Containers\User\Models\AcceptedInvitation;
use Illuminate\Auth\Access\HandlesAuthorization;

class AcceptedInvitationPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_accepted::invitation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, AcceptedInvitation $acceptedInvitation): bool
    {
        return $user->can('view_accepted::invitation');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_accepted::invitation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, AcceptedInvitation $acceptedInvitation): bool
    {
        return $user->can('update_accepted::invitation');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, AcceptedInvitation $acceptedInvitation): bool
    {
        return $user->can('delete_accepted::invitation');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_accepted::invitation');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, AcceptedInvitation $acceptedInvitation): bool
    {
        return $user->can('{{ ForceDelete }}');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('{{ ForceDeleteAny }}');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, AcceptedInvitation $acceptedInvitation): bool
    {
        return $user->can('{{ Restore }}');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('{{ RestoreAny }}');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, AcceptedInvitation $acceptedInvitation): bool
    {
        return $user->can('{{ Replicate }}');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('{{ Reorder }}');
    }
}
