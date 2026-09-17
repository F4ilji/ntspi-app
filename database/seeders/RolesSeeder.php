<?php

namespace Database\Seeders;

use BezhanSalleh\FilamentShield\FilamentShield;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    public function run(): void
    {
        FilamentShield::createRole(
            name: config('filament-shield.super_admin.name', 'super_admin')
        );

        FilamentShield::createRole(
            name: config('filament-shield.dashboard_user.name', 'dashboard_user')
        );

        FilamentShield::createRole(
            name: config('filament-shield.invited_user.name', 'invited_user')
        );

        $adminRole = Role::findByName('super_admin');
        $allPerms = Permission::where('guard_name', 'web')->get();
        $adminRole->syncPermissions($allPerms);
    }
}
