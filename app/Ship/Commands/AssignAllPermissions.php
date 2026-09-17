<?php

namespace App\Ship\Commands;

use App\Ship\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;
use App\Containers\User\Models\User;
use BezhanSalleh\FilamentShield\FilamentShield;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignAllPermissions extends AbstractConsoleCommand
{
    protected $signature = 'user:assign-all-permissions {email?}';

    protected $description = 'Assign super_admin role with all permissions to a user';

    public function handle(): int
    {
        $email = $this->argument('email') ?: 'admin@ntspi.ru';

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User [{$email}] not found.");
            return static::FAILURE;
        }

        $role = Role::firstOrCreate(
            ['name' => 'super_admin', 'guard_name' => 'web']
        );

        $allPerms = Permission::where('guard_name', 'web')->get();
        $role->syncPermissions($allPerms);

        $user->syncRoles(['super_admin']);

        $this->info("User [{$email}] assigned to [super_admin] with {$allPerms->count()} permissions.");
        return static::SUCCESS;
    }
}
