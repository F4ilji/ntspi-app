<?php

namespace App\Containers\VikonIntegration\Providers;

use App\Containers\VikonIntegration\Actions\Auth\AuthenticateAction;
use App\Containers\VikonIntegration\Actions\CheckAccessAction;
use App\Containers\VikonIntegration\Actions\CheckVersionAction;
use App\Containers\VikonIntegration\Actions\SyncFilesAction;
use App\Containers\VikonIntegration\Actions\UpdateCoreAction;
use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\RefreshTokenTask;
use App\Containers\VikonIntegration\Tasks\ValidateTokenTask;
use Illuminate\Support\ServiceProvider;

class VikonServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(config_path('vikon.php'), 'vikon');

        $this->app->singleton(HttpTask::class, fn () => new HttpTask(
            apiDomain: config('vikon.api_domain'),
            authDomain: config('vikon.auth_domain'),
            filemanagerDomain: config('vikon.filemanager_domain'),
            timeout: config('vikon.http_timeout', 60),
            retries: config('vikon.http_retries', 3),
        ));

        $this->app->singleton(ValidateTokenTask::class, fn ($app) => new ValidateTokenTask(
            http: $app->make(HttpTask::class),
            clientId: config('vikon.client_id'),
        ));

        $this->app->singleton(RefreshTokenTask::class, fn ($app) => new RefreshTokenTask(
            http: $app->make(HttpTask::class),
            clientId: config('vikon.client_id'),
            clientSecret: config('vikon.client_secret'),
        ));

        $this->app->singleton(FilesystemTask::class, fn () => new FilesystemTask);

        $this->app->singleton(AuthenticateAction::class, fn ($app) => new AuthenticateAction(
            http: $app->make(HttpTask::class),
            clientId: config('vikon.client_id'),
            clientSecret: config('vikon.client_secret'),
        ));

        $this->app->singleton(CheckAccessAction::class, fn ($app) => new CheckAccessAction(
            validateToken: $app->make(ValidateTokenTask::class),
            http: $app->make(HttpTask::class),
            publicPath: public_path(),
        ));

        $this->app->singleton(CheckVersionAction::class, fn ($app) => new CheckVersionAction(
            http: $app->make(HttpTask::class),
            currentVersion: config('vikon.current_version', '1.0.0'),
        ));

        $this->app->singleton(UpdateCoreAction::class, fn ($app) => new UpdateCoreAction(
            http: $app->make(HttpTask::class),
            fs: $app->make(FilesystemTask::class),
            modulesConfig: config('vikon.modules'),
            storagePath: config('vikon.storage_path'),
            basePath: public_path(),
        ));

        $this->app->singleton(SyncFilesAction::class, fn ($app) => new SyncFilesAction(
            http: $app->make(HttpTask::class),
            publicPath: public_path(),
        ));
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(app_path('Containers/VikonIntegration/UI/WEB/Routes/web.php'));
    }
}
