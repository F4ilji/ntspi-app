<?php

namespace App\Containers\Dashboard\Actions\UserDetails;

use App\Containers\User\Models\UserDetail;

class UpdateUserDetailAction
{
    public function run(UserDetail $userDetail, array $data): UserDetail
    {
        $userDetail->update($data);
        return $userDetail->fresh();
    }
}
