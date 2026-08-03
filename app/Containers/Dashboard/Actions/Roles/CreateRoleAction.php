<?php

namespace App\Containers\Dashboard\Actions\Roles;

use Spatie\Permission\Models\Role;

class CreateRoleAction
{
    public function run(array $data): Role
    {
        $role = Role::create(['name' => $data['name']]);

        if (!empty($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        return $role;
    }
}
