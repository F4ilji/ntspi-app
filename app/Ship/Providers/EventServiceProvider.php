<?php

namespace App\Ship\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Ship\Abstracts\Providers\EventServiceProvider as AbstractEventServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends AbstractEventServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->environment('production')) {
            Event::listen(\Illuminate\Console\Events\CommandStarting::class, function ($event) {
                $blocked = ['migrate:fresh', 'migrate:refresh', 'migrate:reset', 'db:wipe', 'db:seed'];
                if (in_array($event->command, $blocked)) {
                    throw new \RuntimeException("BLOCKED: {$event->command} is disabled in production.");
                }
            });
        }
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
