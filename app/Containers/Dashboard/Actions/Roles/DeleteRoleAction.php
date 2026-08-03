<?php

namespace App\Containers\Dashboard\Actions\Roles;

use Spatie\Permission\Models\Role;

class DeleteRoleAction
{
    public function run(Role $role): void
    {
        $role->delete();
    }
}
