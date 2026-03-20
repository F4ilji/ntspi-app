<?php

namespace App\Ship\Providers;

use Illuminate\Support\Facades\Broadcast;
use App\Ship\Abstracts\Providers\ServiceProvider as AbstractServiceProvider;

class BroadcastServiceProvider extends AbstractServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Broadcast::routes();

        require base_path('routes/channels.php');
    }
}
