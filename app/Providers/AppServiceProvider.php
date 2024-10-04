<?php

namespace App\Providers;

use App\Models\Post;
use App\Observers\PostObserver;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Sleep;
use Intervention\Image\Facades\Image;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale(config('app.locale'));
        Model::preventLazyLoading(!app()->isProduction());
        URL::forceScheme('https');
        self::registerFilamentNavigationGroups();
    }


    private static function registerFilamentNavigationGroups()
    {
        Filament::registerNavigationGroups([
            'Структура приложения',
            'Структура института',
            'Образование',
            'Расписание и группы',
            'Новости и мероприятия',
            'Библиотека',
        ]);
    }
}
