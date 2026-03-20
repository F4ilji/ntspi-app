<?php

namespace App\Ship\Kernels;

use AlxDorosenco\PortoForLaravel\Loaders\CommandsLoader;
use AlxDorosenco\PortoForLaravel\Loaders\RoutesLoader;
use App\Ship\Commands\InitRoles;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as LaravelConsoleKernel;

class ConsoleKernel extends LaravelConsoleKernel
{
    use CommandsLoader;
    use RoutesLoader;

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('sitemap:generate')->dailyAt('03:00');
    }

    protected $commands = [
        InitRoles::class,
    ];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->loadCommandsForConsoleKernel();
        $this->loadRoutesForConsoleKernel();
    }
}
