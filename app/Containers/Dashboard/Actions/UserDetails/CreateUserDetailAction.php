<?php

namespace App\Containers\Dashboard\Actions\UserDetails;

use App\Containers\User\Models\UserDetail;

class CreateUserDetailAction
{
    public function run(int $userId, array $data): UserDetail
    {
        $data['user_id'] = $userId;

        return UserDetail::create($data);
    }
}
