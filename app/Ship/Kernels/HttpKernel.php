<?php

namespace App\Ship\Kernels;

use App\Ship\Middleware\AccessCheck;
use App\Ship\Middleware\EnsureUserIsSuperadmin;
use App\Ship\Middleware\FormTimePeriodMiddleware;
use App\Ship\Middleware\InternalRequestOnly;
use App\Ship\Middleware\LimitPost;
use App\Ship\Middleware\RateLimitCheckMiddleware;
use App\Ship\Middleware\RateLimitCounterMiddleware;
use App\Ship\Middleware\TransformRequestMiddleware;
use Illuminate\Foundation\Http\Kernel as LaravelHttpKernel;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

class HttpKernel extends LaravelHttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array<int, class-string|string>
     */
    protected $middleware = [
        TransformRequestMiddleware::class,
        // \App\Ship\Middleware\TrustHosts::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \App\Ship\Middleware\TrustProxies::class,
        \Illuminate\Http\Middleware\HandleCors::class,
        \App\Ship\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Ship\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Ship\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Ship\Middleware\VerifyCsrfToken::class,
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            // \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array<string, class-string|string>
     */

    protected $routeMiddleware = [
        'auth' => \App\Ship\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Ship\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \App\Ship\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
        'role' => RoleMiddleware::class,
        'permission' => PermissionMiddleware::class,
        'role_or_permission' => RoleOrPermissionMiddleware::class,
        'access-check' => AccessCheck::class,
        'rate.limited.counter' => RateLimitCounterMiddleware::class,
        'rate.limited.check' => RateLimitCheckMiddleware::class,
        'ensure.browser' => InternalRequestOnly::class,
        'superadmin' => EnsureUserIsSuperadmin::class,
        'limit.post' => LimitPost::class,
        'form.time.period' => FormTimePeriodMiddleware::class,
    ];
}
