<?php

namespace App\Containers\Analytics\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;

class AnalyticsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \App\Containers\Analytics\Commands\AggregateAnalyticsCommand::class,
            ]);

            $this->app->afterResolving(Schedule::class, function (Schedule $schedule) {
                $schedule->command('analytics:aggregate', ['--days' => 1])->hourly();
            });
        }
    }
}
