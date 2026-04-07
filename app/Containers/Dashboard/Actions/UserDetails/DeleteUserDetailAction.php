<?php

namespace App\Containers\Dashboard\Actions\UserDetails;

use App\Containers\User\Models\UserDetail;

class DeleteUserDetailAction
{
    public function run(UserDetail $userDetail): bool
    {
        return $userDetail->delete();
    }
}
