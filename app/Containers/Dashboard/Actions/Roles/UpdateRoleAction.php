<?php

namespace App\Containers\Dashboard\Actions\Roles;

use Spatie\Permission\Models\Role;

class UpdateRoleAction
{
    public function run(Role $role, array $data): Role
    {
        $role->update(['name' => $data['name']]);

        $role->syncPermissions($data['permissions'] ?? []);

        return $role;
    }
}
