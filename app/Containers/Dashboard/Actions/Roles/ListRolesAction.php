<?php

namespace App\Containers\Dashboard\Actions\Roles;

use Spatie\Permission\Models\Role;

class ListRolesAction
{
    public function run(): array
    {
        $roles = Role::with('permissions')->orderBy('name')->get();

        return [
            'roles' => $roles,
        ];
    }
}
