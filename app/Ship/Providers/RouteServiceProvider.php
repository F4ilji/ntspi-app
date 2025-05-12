<?php

namespace App\Ship\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use App\Ship\Abstracts\Providers\RouteServiceProvider as AbstractRouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class RouteServiceProvider extends AbstractRouteServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    public function boot()
    {

    }
}
