<?php

namespace App\Ship\Kernels;

use AlxDorosenco\PortoForLaravel\Loaders\CommandsLoader;
use AlxDorosenco\PortoForLaravel\Loaders\RoutesLoader;
use App\Containers\Dashboard\Commands\FetchEmailNewsCommand;
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
        
        // Получение новостей из Email каждые 5 минут
        $schedule->command(FetchEmailNewsCommand::class)
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->onOneServer();
    }

    protected $commands = [
        InitRoles::class,
        FetchEmailNewsCommand::class,
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
