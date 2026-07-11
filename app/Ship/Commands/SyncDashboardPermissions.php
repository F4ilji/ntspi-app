<?php

namespace App\Ship\Commands;

use App\Ship\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SyncDashboardPermissions extends AbstractConsoleCommand
{
    protected $signature = 'dashboard:sync-permissions {--role=super_admin,admin : Roles to sync all permissions to}';

    protected $description = 'Create missing dashboard permissions and sync to roles';

    private const RESOURCES = [
        'post', 'category', 'department', 'faculty', 'slider',
        'user', 'role', 'page', 'news', 'menu', 'widget',
        'contactwidget', 'additional_education', 'admission_campaign',
        'schedule', 'academic_journal', 'main_section', 'sub_section',
        'direction_study', 'educational_program', 'admission_plan',
        'educational_group', 'division',
    ];

    private const PREFIXES = [
        'view_any', 'create', 'update', 'delete', 'restore', 'force_delete',
    ];

    public function handle(): int
    {
        $created = 0;

        foreach (self::RESOURCES as $resource) {
            foreach (self::PREFIXES as $prefix) {
                $name = "{$prefix}_{$resource}";

                Permission::firstOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                );

                $created++;
            }
        }

        // Also create view_any_contact_widget (with underscore) if menuConfig uses it
        Permission::firstOrCreate(
            ['name' => 'view_any_contact_widget', 'guard_name' => 'web'],
        );

        $roleNames = explode(',', $this->option('role'));
        $allPerms = Permission::where('guard_name', 'web')->get();

        foreach ($roleNames as $roleName) {
            $roleName = trim($roleName);
            $role = Role::firstOrCreate(
                ['name' => $roleName, 'guard_name' => 'web'],
            );
            $role->syncPermissions($allPerms);
            $this->info("Role [{$roleName}] synced with {$allPerms->count()} permissions");
        }

        $this->info("Done. Total permissions: {$allPerms->count()}");

        return static::SUCCESS;
    }
}
