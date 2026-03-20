<?php

namespace App\Ship\Commands;
use BezhanSalleh\FilamentShield\FilamentShield;


use App\Ship\Abstracts\Commands\ConsoleCommand as AbstractConsoleCommand;

class InitRoles extends AbstractConsoleCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:init-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes default roles for Filament Shield.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        // Проверяем, что Filament Shield включен
        if (!config('filament-shield.dashboard_user.enabled', false)) {
            $this->warn('Filament Shield dashboard user is not enabled in the config.');
            return;
        }

        // Создаем роли
        FilamentShield::createRole(
            name: config('filament-shield.dashboard_user.name', 'dashboard_user')
        );
        FilamentShield::createRole(
            name: config('', 'editor')
        );

        $this->info('Filament Shield roles have been initialized successfully.');
    }
}
