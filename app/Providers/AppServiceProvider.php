<?php

namespace App\Providers;

use App\Models\MainSection;
use App\Models\MainSlider;
use App\Models\Page;
use App\Models\Post;
use App\Models\SubSection;
use App\Observers\MainSectionObserver;
use App\Observers\MainSliderObserver;
use App\Observers\PageObserver;
use App\Observers\PostObserver;
use App\Observers\SubSectionObserver;
use App\Services\App\Cache\MainSliderCacheService;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
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
        self::setObserversByModel();
        self::setLocaleTime();
        Model::preventLazyLoading(!app()->isProduction());
//        URL::forceScheme('https');
        self::registerFilamentNavigationGroups();
        $this->loadViewsFrom(__DIR__.'/path/to/views', 'checkpoint');

        FilamentView::registerRenderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, function () {
            (new MainSliderCacheService())->clearAllCacheByModel();
        });
    }

    private static function setObserversByModel() : void {
        Page::observe(PageObserver::class);
        Post::observe(PostObserver::class);
        MainSection::observe(MainSectionObserver::class);
        SubSection::observe(SubSectionObserver::class);
        MainSlider::observe(MainSliderObserver::class);
    }

    private static function setLocaleTime() : void {
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale(config('app.locale'));
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
