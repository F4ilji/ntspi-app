<?php

namespace App\Providers;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\AppStructure\Models\MainSection;
use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Models\SubSection;
use App\Containers\Article\Models\Post;
use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Education\Models\AdmissionPlan;
use App\Containers\Education\Models\DirectionStudy;
use App\Containers\Education\Models\EducationalProgram;
use App\Containers\Event\Models\Event;
use App\Containers\InstituteStructure\Models\Department;
use App\Containers\InstituteStructure\Models\Division;
use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\Schedule\Models\Schedule;
use App\Containers\Science\Models\AcademicJournal;
use App\Containers\User\Models\User;
use App\Containers\Widget\Models\ContactWidget;
use App\Containers\Widget\Models\PageReferenceList;
use App\Containers\Widget\Models\Slide;
use App\Observers\AcademicJournalObserver;
use App\Observers\AdditionalEducationObserver;
use App\Observers\AdmissionCampaignObserver;
use App\Observers\ContactWidgetObserver;
use App\Observers\DepartmentObserver;
use App\Observers\DivisionObserver;
use App\Observers\EducationalProgramObserver;
use App\Observers\EventObserver;
use App\Observers\FacultyObserver;
use App\Observers\MainSectionObserver;
use App\Observers\PageObserver;
use App\Observers\PageReferenceListObserver;
use App\Observers\PostObserver;
use App\Observers\ScheduleObserver;
use App\Observers\SlideObserver;
use App\Observers\SubSectionObserver;
use App\Observers\UserObserver;
use App\Services\App\Cache\SliderCacheService;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Services\SeoPageService;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentView;
use Filament\Tables\View\TablesRenderHook;
use Illuminate\Database\Eloquent\Model;
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
        $this->app->bind(SeoServiceInterface::class, SeoPageService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        self::setObserversByModel();
        self::setLocaleTime();
        Model::preventLazyLoading(!app()->isProduction());
        self::registerFilamentNavigationGroups();
        $this->loadViewsFrom(__DIR__.'/path/to/views', 'checkpoint');

        FilamentView::registerRenderHook(TablesRenderHook::TOOLBAR_REORDER_TRIGGER_AFTER, function () {
            (new SliderCacheService())->clearAllCacheByModel();
        });
    }

    private static function setObserversByModel() : void {
        Page::observe(PageObserver::class);
        Post::observe(PostObserver::class);
        MainSection::observe(MainSectionObserver::class);
        SubSection::observe(SubSectionObserver::class);
        Slide::observe(SlideObserver::class);
        AcademicJournal::observe(AcademicJournalObserver::class);
        AdditionalEducation::observe(AdditionalEducationObserver::class);
        Department::observe(DepartmentObserver::class);
        Division::observe(DivisionObserver::class);
        Event::observe(EventObserver::class);
        Faculty::observe(FacultyObserver::class);
        User::observe(UserObserver::class);
        EducationalProgram::observe(EducationalProgramObserver::class);
        Schedule::observe(ScheduleObserver::class);
        ContactWidget::observe(ContactWidgetObserver::class);
        PageReferenceList::observe(PageReferenceListObserver::class);
        AdmissionPlan::observe(AdmissionCampaignObserver::class);
        AdmissionCampaign::observe(AdmissionCampaignObserver::class);
        DirectionStudy::observe(AdmissionCampaignObserver::class);
    }

    private static function setLocaleTime() : void {
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        Carbon::setLocale(config('app.locale'));
    }


    private static function registerFilamentNavigationGroups(): void
    {
        Filament::registerNavigationGroups([
            'Виджеты',
            'Новости и мероприятия',
            'Структура приложения',
            'Структура института',
            'Образование',
            'Расписание и группы',
            'Наука',
            'Settings',
        ]);
    }
}
