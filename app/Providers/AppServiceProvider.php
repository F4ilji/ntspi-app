<?php

namespace App\Providers;

use App\Models\AcademicJournal;
use App\Models\AdditionalEducation;
use App\Models\AdmissionCampaign;
use App\Models\AdmissionPlan;
use App\Models\ContactWidget;
use App\Models\Department;
use App\Models\DirectionStudy;
use App\Models\Division;
use App\Models\EducationalProgram;
use App\Models\Event;
use App\Models\Faculty;
use App\Models\MainSection;
use App\Models\MainSlider;
use App\Models\Page;
use App\Models\PageReferenceList;
use App\Models\Post;
use App\Models\Schedule;
use App\Models\Slide;
use App\Models\SubSection;
use App\Models\User;
use App\Observers\AcademicJournalObserver;
use App\Observers\AdditionalEducationObserver;
use App\Observers\AdmissionCampaignObserver;
use App\Observers\AdmissionPlanObserver;
use App\Observers\ContactWidgetObserver;
use App\Observers\DepartmentObserver;
use App\Observers\DirectionStudyObserver;
use App\Observers\DivisionObserver;
use App\Observers\EducationalProgramObserver;
use App\Observers\EventObserver;
use App\Observers\FacultyObserver;
use App\Observers\MainSectionObserver;
use App\Observers\MainSliderObserver;
use App\Observers\PageObserver;
use App\Observers\PageReferenceListObserver;
use App\Observers\PostObserver;
use App\Observers\ScheduleObserver;
use App\Observers\SlideObserver;
use App\Observers\SubSectionObserver;
use App\Observers\UserObserver;
use App\Services\App\Cache\MainSliderCacheService;
use App\Services\App\Cache\SliderCacheService;
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
