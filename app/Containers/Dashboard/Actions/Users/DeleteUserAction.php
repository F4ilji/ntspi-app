<?php

namespace App\Containers\Dashboard\Actions\Users;

use App\Containers\User\Models\User;

class DeleteUserAction
{
    public function run(User $user): bool
    {
        return $user->delete();
    }
}
