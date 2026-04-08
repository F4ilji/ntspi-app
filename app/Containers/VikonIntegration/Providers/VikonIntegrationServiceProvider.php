<?php

namespace App\Containers\VikonIntegration\Providers;

use App\Containers\VikonIntegration\Actions\Auth\AuthenticateVikonAction;
use App\Containers\VikonIntegration\Actions\Updates\CheckVikonAccessAction;
use App\Containers\VikonIntegration\Actions\Updates\CheckVikonVersionAction;
use App\Containers\VikonIntegration\Actions\Updates\DownloadModuleUpdateAction;
use App\Containers\VikonIntegration\Actions\Updates\SyncModuleFilesAction;
use App\Containers\VikonIntegration\Tasks\CallVikonApiTask;
use App\Containers\VikonIntegration\Tasks\CheckVikonEntryPointTask;
use App\Containers\VikonIntegration\Tasks\ExtractZipArchiveTask;
use App\Containers\VikonIntegration\Tasks\ManageModuleFilesTask;
use App\Containers\VikonIntegration\Tasks\RefreshVikonTokenTask;
use App\Containers\VikonIntegration\Tasks\ValidateVikonTokenTask;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class VikonIntegrationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(config_path('vikon.php'), 'vikon');

        // Register Tasks with dependencies from config
        $this->app->singleton(CallVikonApiTask::class, function ($app) {
            return new CallVikonApiTask(
                apiDomain: config('vikon.api_domain'),
                authDomain: config('vikon.auth_domain'),
                filemanagerDomain: config('vikon.filemanager_domain'),
                timeout: config('vikon.http_timeout', 60),
                retries: config('vikon.http_retries', 3),
            );
        });

        $this->app->singleton(ExtractZipArchiveTask::class);

        $this->app->singleton(ValidateVikonTokenTask::class, function ($app) {
            return new ValidateVikonTokenTask(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
            );
        });

        $this->app->singleton(RefreshVikonTokenTask::class, function ($app) {
            return new RefreshVikonTokenTask(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
                clientId: config('vikon.client_id'),
                clientSecret: config('vikon.client_secret'),
            );
        });

        $this->app->singleton(CheckVikonEntryPointTask::class, function ($app) {
            return new CheckVikonEntryPointTask(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
                clientId: config('vikon.client_id'),
            );
        });

        $this->app->singleton(ManageModuleFilesTask::class, function ($app) {
            return new ManageModuleFilesTask(
                basePath: public_path(),
            );
        });

        // Register Actions
        $this->app->singleton(AuthenticateVikonAction::class, function ($app) {
            return new AuthenticateVikonAction(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
                clientId: config('vikon.client_id', ''),
                clientSecret: config('vikon.client_secret', ''),
            );
        });

        $this->app->singleton(ValidateVikonTokenTask::class, function ($app) {
            return new ValidateVikonTokenTask(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
            );
        });

        $this->app->singleton(CheckVikonAccessAction::class, function ($app) {
            return new CheckVikonAccessAction(
                validateTokenTask: $app->make(ValidateVikonTokenTask::class),
                callVikonApiTask: $app->make(CallVikonApiTask::class),
                publicPath: public_path(),
            );
        });

        $this->app->singleton(CheckVikonVersionAction::class, function ($app) {
            return new CheckVikonVersionAction(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
                currentVersion: config('vikon.current_version', '1.0.0'),
            );
        });

        $this->app->singleton(DownloadModuleUpdateAction::class, function ($app) {
            return new DownloadModuleUpdateAction(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
                extractZipTask: $app->make(ExtractZipArchiveTask::class),
                modulesConfig: config('vikon.modules'),
                storagePath: config('vikon.storage_path'),
                basePath: public_path(),
            );
        });

        $this->app->singleton(SyncModuleFilesAction::class, function ($app) {
            return new SyncModuleFilesAction(
                callVikonApiTask: $app->make(CallVikonApiTask::class),
                modulesConfig: config('vikon.modules'),
                basePath: public_path(),
            );
        });
    }

    public function boot(): void
    {
        // Load routes
        $this->loadRoutesFrom(app_path('Containers/VikonIntegration/UI/WEB/Routes/web.php'));

        // Publish config
        $this->publishes([
            config_path('vikon.php') => config_path('vikon.php'),
        ], 'vikon-config');
    }
}
