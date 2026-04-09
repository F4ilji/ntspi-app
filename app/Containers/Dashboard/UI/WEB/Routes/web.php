<?php

use App\Containers\Dashboard\UI\WEB\Controllers\AcademicJournalController;
use App\Containers\Dashboard\UI\WEB\Controllers\AdmissionCampaignController;
use App\Containers\Dashboard\UI\WEB\Controllers\AdmissionPlanController;
use App\Containers\Dashboard\UI\WEB\Controllers\AdditionalEducationController;
use App\Containers\Dashboard\UI\WEB\Controllers\AdditionalEducations\CategoryController as AdditionalEducationCategoryController;
use App\Containers\Dashboard\UI\WEB\Controllers\AdditionalEducations\DirectionController;
use App\Containers\Dashboard\UI\WEB\Controllers\CategoryController as NewsCategoryController;
use App\Containers\Dashboard\UI\WEB\Controllers\ContactWidgetController;
use App\Containers\Dashboard\UI\WEB\Controllers\CreateSliderController;
use App\Containers\Dashboard\UI\WEB\Controllers\CustomFormController;
use App\Containers\Dashboard\UI\WEB\Controllers\DeployController;
use App\Containers\Dashboard\UI\WEB\Controllers\DepartmentController;
use App\Containers\Dashboard\UI\WEB\Controllers\PageReferenceListController;
use App\Containers\Dashboard\UI\WEB\Controllers\DepartmentProgramController;
use App\Containers\Dashboard\UI\WEB\Controllers\DepartmentTeacherController;
use App\Containers\Dashboard\UI\WEB\Controllers\DepartmentWorkerController;
use App\Containers\Dashboard\UI\WEB\Controllers\DestroySlideController;
use App\Containers\Dashboard\UI\WEB\Controllers\DestroySliderController;
use App\Containers\Dashboard\UI\WEB\Controllers\DirectionStudyController;
use App\Containers\Dashboard\UI\WEB\Controllers\DivisionController;
use App\Containers\Dashboard\UI\WEB\Controllers\DivisionWorkerController;
use App\Containers\Dashboard\UI\WEB\Controllers\EducationalGroupController;
use App\Containers\Dashboard\UI\WEB\Controllers\EducationalProgramController;
use App\Containers\Dashboard\UI\WEB\Controllers\EditSliderController;
use App\Containers\Dashboard\UI\WEB\Controllers\FacultyController;
use App\Containers\Dashboard\UI\WEB\Controllers\FacultyWorkerController;
use App\Containers\Dashboard\UI\WEB\Controllers\IndexDashboardController;
use App\Containers\Dashboard\UI\WEB\Controllers\JournalIssueController;
use App\Containers\Dashboard\UI\WEB\Controllers\MainSectionController;
use App\Containers\Dashboard\UI\WEB\Controllers\PageController;
use App\Containers\Dashboard\UI\WEB\Controllers\ParseEmailNewsController;
use App\Containers\Dashboard\UI\WEB\Controllers\PostController;
use App\Containers\Dashboard\UI\WEB\Controllers\ProcessMixedFilesController;
use App\Containers\Dashboard\UI\WEB\Controllers\PublishPostController;
use App\Containers\Dashboard\UI\WEB\Controllers\QuickUploadController;
use App\Containers\Dashboard\UI\WEB\Controllers\ScheduleController;
use App\Containers\Dashboard\UI\WEB\Controllers\SliderController;
use App\Containers\Dashboard\UI\WEB\Controllers\StoreFilesController;
use App\Containers\Dashboard\UI\WEB\Controllers\StoreSlideController;
use App\Containers\Dashboard\UI\WEB\Controllers\StoreSliderController;
use App\Containers\Dashboard\UI\WEB\Controllers\SubSectionController;
use App\Containers\Dashboard\UI\WEB\Controllers\UpdateSlideController;
use App\Containers\Dashboard\UI\WEB\Controllers\UpdateSliderController;
use App\Containers\Dashboard\UI\WEB\Controllers\UpdateSlidesOrderController;
use App\Containers\Dashboard\UI\WEB\Controllers\UploadSchedulesController;
use App\Containers\Dashboard\UI\WEB\Controllers\UserController;
use App\Containers\Dashboard\UI\WEB\Controllers\UserDetailController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;

// Guest routes (Login/Logout)
Route::middleware('guest')->group(function () {
    Route::get('/dashboard/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/dashboard/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/dashboard/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Authenticated dashboard routes
Route::middleware(['access-check', 'dashboard.auth'])->group(function () {
    Route::get('/dashboard', IndexDashboardController::class)->name('dashboard.index');
    Route::post('/dashboard/deploy', [DeployController::class, 'deploy'])->name('dashboard.deploy');
    Route::get('/dashboard/deploy/status', [DeployController::class, 'status'])->name('dashboard.deploy.status');
    Route::post('/dashboard/deploy/clear', [DeployController::class, 'clear'])->name('dashboard.deploy.clear');

    // CRUD постов
    Route::prefix('/dashboard/posts')->name('dashboard.posts.')->group(function () {
        Route::get('/', [PostController::class, 'index'])->name('index');
        Route::get('/ai-prepared', [PostController::class, 'aiPrepared'])->name('ai-prepared');
        Route::post('/ai-prepared/parse-email', [ParseEmailNewsController::class, '__invoke'])->name('ai-prepared.parse-email');
        Route::get('/create', [PostController::class, 'create'])->name('create');
        Route::post('/', [PostController::class, 'store'])->name('store');
        Route::post('/upload-images', [PostController::class, 'uploadImages'])->name('upload-images');
        Route::delete('/bulk-destroy', [PostController::class, 'bulkDestroy'])->name('bulk-destroy');
        Route::post('/bulk-publish', [PostController::class, 'bulkPublish'])->name('bulk-publish');
        Route::post('/bulk-verification', [PostController::class, 'bulkVerification'])->name('bulk-verification');
        Route::get('/{post}', [PostController::class, 'show'])->name('show');
        Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
        Route::put('/{post}', [PostController::class, 'update'])->name('update');
        Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
    });

    // CRUD категорий новостей
    Route::prefix('/dashboard/categories')->name('dashboard.categories.')->group(function () {
        Route::get('/', [NewsCategoryController::class, 'index'])->name('index');
        Route::get('/create', [NewsCategoryController::class, 'create'])->name('create');
        Route::post('/', [NewsCategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [NewsCategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [NewsCategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [NewsCategoryController::class, 'destroy'])->name('destroy');
    });

    // CRUD учебных групп
    Route::prefix('/dashboard/educational-groups')->name('dashboard.educational-groups.')->group(function () {
        Route::get('/', [EducationalGroupController::class, 'index'])->name('index');
        Route::get('/create', [EducationalGroupController::class, 'create'])->name('create');
        Route::post('/', [EducationalGroupController::class, 'store'])->name('store');
        Route::get('/{educationalGroup}/edit', [EducationalGroupController::class, 'edit'])->name('edit');
        Route::put('/{educationalGroup}', [EducationalGroupController::class, 'update'])->name('update');
        Route::delete('/{educationalGroup}', [EducationalGroupController::class, 'destroy'])->name('destroy');
    });

    // CRUD программ дополнительного образования
    Route::prefix('/dashboard/additional-educations')->name('dashboard.additional-educations.')->group(function () {
        Route::get('/', [AdditionalEducationController::class, 'index'])->name('index');
        Route::get('/create', [AdditionalEducationController::class, 'create'])->name('create');
        Route::post('/', [AdditionalEducationController::class, 'store'])->name('store');
        Route::get('/{additionalEducation}/edit', [AdditionalEducationController::class, 'edit'])->name('edit');
        Route::put('/{additionalEducation}', [AdditionalEducationController::class, 'update'])->name('update');
        Route::delete('/{additionalEducation}', [AdditionalEducationController::class, 'destroy'])->name('destroy');

        // Направления ДПО
        Route::prefix('/directions')->name('directions.')->group(function () {
            Route::get('/', [DirectionController::class, 'index'])->name('index');
            Route::get('/create', [DirectionController::class, 'create'])->name('create');
            Route::post('/', [DirectionController::class, 'store'])->name('store');
            Route::get('/{direction}/edit', [DirectionController::class, 'edit'])->name('edit');
            Route::put('/{direction}', [DirectionController::class, 'update'])->name('update');
            Route::delete('/{direction}', [DirectionController::class, 'destroy'])->name('destroy');
        });

        // Категории ДПО
        Route::prefix('/categories')->name('categories.')->group(function () {
            Route::get('/', [AdditionalEducationCategoryController::class, 'index'])->name('index');
            Route::get('/create', [AdditionalEducationCategoryController::class, 'create'])->name('create');
            Route::post('/', [AdditionalEducationCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [AdditionalEducationCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [AdditionalEducationCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [AdditionalEducationCategoryController::class, 'destroy'])->name('destroy');
        });
    });

    // CRUD приемных кампаний
    Route::prefix('/dashboard/admission-campaigns')->name('dashboard.admission-campaigns.')->group(function () {
        Route::get('/', [AdmissionCampaignController::class, 'index'])->name('index');
        Route::get('/create', [AdmissionCampaignController::class, 'create'])->name('create');
        Route::post('/', [AdmissionCampaignController::class, 'store'])->name('store');
        Route::get('/{admissionCampaign}/edit', [AdmissionCampaignController::class, 'edit'])->name('edit');
        Route::put('/{admissionCampaign}', [AdmissionCampaignController::class, 'update'])->name('update');
        Route::delete('/{admissionCampaign}', [AdmissionCampaignController::class, 'destroy'])->name('destroy');
    });

    // CRUD направлений подготовки
    Route::prefix('/dashboard/direction-studies')->name('dashboard.direction-studies.')->group(function () {
        Route::get('/', [DirectionStudyController::class, 'index'])->name('index');
        Route::get('/create', [DirectionStudyController::class, 'create'])->name('create');
        Route::post('/', [DirectionStudyController::class, 'store'])->name('store');
        Route::get('/{directionStudy}/edit', [DirectionStudyController::class, 'edit'])->name('edit');
        Route::put('/{directionStudy}', [DirectionStudyController::class, 'update'])->name('update');
        Route::delete('/{directionStudy}', [DirectionStudyController::class, 'destroy'])->name('destroy');
    });

    // CRUD образовательных программ
    Route::prefix('/dashboard/educational-programs')->name('dashboard.educational-programs.')->group(function () {
        Route::get('/', [EducationalProgramController::class, 'index'])->name('index');
        Route::get('/create', [EducationalProgramController::class, 'create'])->name('create');
        Route::post('/', [EducationalProgramController::class, 'store'])->name('store');
        Route::get('/{educationalProgram}/edit', [EducationalProgramController::class, 'edit'])->name('edit');
        Route::put('/{educationalProgram}', [EducationalProgramController::class, 'update'])->name('update');
        Route::delete('/{educationalProgram}', [EducationalProgramController::class, 'destroy'])->name('destroy');
    });

    // CRUD планов приема
    Route::prefix('/dashboard/admission-plans')->name('dashboard.admission-plans.')->group(function () {
        Route::get('/', [AdmissionPlanController::class, 'index'])->name('index');
        Route::get('/create', [AdmissionPlanController::class, 'create'])->name('create');
        Route::post('/', [AdmissionPlanController::class, 'store'])->name('store');
        Route::get('/{admissionPlan}/edit', [AdmissionPlanController::class, 'edit'])->name('edit');
        Route::put('/{admissionPlan}', [AdmissionPlanController::class, 'update'])->name('update');
        Route::delete('/{admissionPlan}', [AdmissionPlanController::class, 'destroy'])->name('destroy');
    });

    // CRUD научных журналов
    Route::prefix('/dashboard/academic-journals')->name('dashboard.academic-journals.')->group(function () {
        Route::get('/', [AcademicJournalController::class, 'index'])->name('index');
        Route::get('/create', [AcademicJournalController::class, 'create'])->name('create');
        Route::post('/', [AcademicJournalController::class, 'store'])->name('store');
        Route::get('/{academicJournal}/edit', [AcademicJournalController::class, 'edit'])->name('edit');
        Route::put('/{academicJournal}', [AcademicJournalController::class, 'update'])->name('update');
        Route::delete('/{academicJournal}', [AcademicJournalController::class, 'destroy'])->name('destroy');

        // Выпуски журналов (Relation Manager)
        Route::prefix('/{academicJournal}/issues')->name('issues.')->group(function () {
            Route::get('/', [JournalIssueController::class, 'index'])->name('index');
            Route::get('/create', [JournalIssueController::class, 'create'])->name('create');
            Route::post('/', [JournalIssueController::class, 'store'])->name('store');
            Route::get('/{issue}/edit', [JournalIssueController::class, 'edit'])->name('edit');
            Route::put('/{issue}', [JournalIssueController::class, 'update'])->name('update');
            Route::delete('/{issue}', [JournalIssueController::class, 'destroy'])->name('destroy');
        });
    });

    // CRUD расписаний
    Route::prefix('/dashboard/schedules')->name('dashboard.schedules.')->group(function () {
        Route::get('/', [ScheduleController::class, 'index'])->name('index');
        Route::get('/create', [ScheduleController::class, 'create'])->name('create');
        Route::post('/', [ScheduleController::class, 'store'])->name('store');
        Route::get('/{schedule}/edit', [ScheduleController::class, 'edit'])->name('edit');
        Route::put('/{schedule}', [ScheduleController::class, 'update'])->name('update');
        Route::delete('/{schedule}', [ScheduleController::class, 'destroy'])->name('destroy');
    });

    // Быстрая загрузка расписаний
    Route::prefix('/dashboard/schedules/upload')->name('dashboard.schedules.upload.')->group(function () {
        Route::get('/', [UploadSchedulesController::class, 'create'])->name('create');
        Route::post('/', [UploadSchedulesController::class, 'store'])->name('store');
    });

    // Смешанная загрузка (все файлы в одном поле)
    Route::post('/dashboard/files/store', ProcessMixedFilesController::class)->name('dashboard.files.store');

    // Раздельная загрузка (document + media) - для будущего использования
    Route::post('/dashboard/files/store-separate', StoreFilesController::class)->name('dashboard.files.store-separate');

    // Публикация черновика поста
    Route::post('/dashboard/posts/{post}/publish', PublishPostController::class)->name('dashboard.posts.publish');

    // Быстрая загрузка файла
    Route::prefix('/dashboard/quick-upload')->name('dashboard.quick-upload.')->group(function () {
        Route::get('/', [QuickUploadController::class, 'create'])->name('create');
        Route::post('/', [QuickUploadController::class, 'store'])->name('store');
    });

    // CRUD слайдеров
    Route::prefix('/dashboard/sliders')->name('dashboard.sliders.')->group(function () {
        Route::get('/', [SliderController::class, 'index'])->name('index');
        Route::get('/create', [CreateSliderController::class, '__invoke'])->name('create');
        Route::post('/', [StoreSliderController::class, '__invoke'])->name('store');
        Route::get('/{slider}/edit', [EditSliderController::class, '__invoke'])->name('edit');
        Route::put('/{slider}', [UpdateSliderController::class, '__invoke'])->name('update');
        Route::delete('/{slider}', [DestroySliderController::class, '__invoke'])->name('destroy');
    });

    // CRUD слайдов
    Route::prefix('/dashboard/slides')->name('dashboard.slides.')->group(function () {
        Route::post('/slider/{slider}', [StoreSlideController::class, '__invoke'])->name('store');
        Route::put('/{slide}', [UpdateSlideController::class, '__invoke'])->name('update');
        Route::delete('/{slide}', [DestroySlideController::class, '__invoke'])->name('destroy');
        Route::put('/slider/{slider}/order', [UpdateSlidesOrderController::class, '__invoke'])->name('order');
    });

    // CRUD факультетов
    Route::prefix('/dashboard/faculties')->name('dashboard.faculties.')->group(function () {
        Route::get('/', [FacultyController::class, 'index'])->name('index');
        Route::get('/create', [FacultyController::class, 'create'])->name('create');
        Route::post('/', [FacultyController::class, 'store'])->name('store');
        Route::get('/{faculty}/edit', [FacultyController::class, 'edit'])->name('edit');
        Route::put('/{faculty}', [FacultyController::class, 'update'])->name('update');
        Route::delete('/{faculty}', [FacultyController::class, 'destroy'])->name('destroy');
    });

    // Сотрудники факультетов
    Route::prefix('/dashboard/faculties/{faculty}/workers')->name('dashboard.faculties.workers.')->group(function () {
        Route::get('/', [FacultyWorkerController::class, 'index'])->name('index');
        Route::post('/', [FacultyWorkerController::class, 'attach'])->name('attach');
        Route::put('/{worker}', [FacultyWorkerController::class, 'update'])->name('update');
        Route::delete('/{worker}', [FacultyWorkerController::class, 'detach'])->name('detach');
    });

    // CRUD подразделений
    Route::prefix('/dashboard/divisions')->name('dashboard.divisions.')->group(function () {
        Route::get('/', [DivisionController::class, 'index'])->name('index');
        Route::get('/create', [DivisionController::class, 'create'])->name('create');
        Route::post('/', [DivisionController::class, 'store'])->name('store');
        Route::get('/{division}/edit', [DivisionController::class, 'edit'])->name('edit');
        Route::put('/{division}', [DivisionController::class, 'update'])->name('update');
        Route::delete('/{division}', [DivisionController::class, 'destroy'])->name('destroy');
    });

    // Сотрудники подразделений
    Route::prefix('/dashboard/divisions/{division}/workers')->name('dashboard.divisions.workers.')->group(function () {
        Route::get('/', [DivisionWorkerController::class, 'index'])->name('index');
        Route::post('/', [DivisionWorkerController::class, 'attach'])->name('attach');
        Route::put('/{worker}', [DivisionWorkerController::class, 'update'])->name('update');
        Route::delete('/{worker}', [DivisionWorkerController::class, 'detach'])->name('detach');
    });

    // CRUD кафедр
    Route::prefix('/dashboard/departments')->name('dashboard.departments.')->group(function () {
        Route::get('/', [DepartmentController::class, 'index'])->name('index');
        Route::get('/create', [DepartmentController::class, 'create'])->name('create');
        Route::post('/', [DepartmentController::class, 'store'])->name('store');
        Route::get('/{department}/edit', [DepartmentController::class, 'edit'])->name('edit');
        Route::put('/{department}', [DepartmentController::class, 'update'])->name('update');
        Route::delete('/{department}', [DepartmentController::class, 'destroy'])->name('destroy');
    });

    // Сотрудники кафедр
    Route::prefix('/dashboard/departments/{department}/workers')->name('dashboard.departments.workers.')->group(function () {
        Route::get('/', [DepartmentWorkerController::class, 'index'])->name('index');
        Route::post('/', [DepartmentWorkerController::class, 'attach'])->name('attach');
        Route::put('/{worker}', [DepartmentWorkerController::class, 'update'])->name('update');
        Route::delete('/{worker}', [DepartmentWorkerController::class, 'detach'])->name('detach');
    });

    // Преподаватели кафедр
    Route::prefix('/dashboard/departments/{department}/teachers')->name('dashboard.departments.teachers.')->group(function () {
        Route::get('/', [DepartmentTeacherController::class, 'index'])->name('index');
        Route::post('/', [DepartmentTeacherController::class, 'attach'])->name('attach');
        Route::put('/{teacher}', [DepartmentTeacherController::class, 'update'])->name('update');
        Route::delete('/{teacher}', [DepartmentTeacherController::class, 'detach'])->name('detach');
    });

    // Образовательные программы кафедр
    Route::prefix('/dashboard/departments/{department}/programs')->name('dashboard.departments.programs.')->group(function () {
        Route::get('/', [DepartmentProgramController::class, 'index'])->name('index');
        Route::post('/', [DepartmentProgramController::class, 'attach'])->name('attach');
        Route::delete('/{program}', [DepartmentProgramController::class, 'detach'])->name('detach');
    });

    // CRUD пользователей
    Route::prefix('/dashboard/users')->name('dashboard.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index');
        Route::get('/create', [UserController::class, 'create'])->name('create');
        Route::post('/', [UserController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
        Route::put('/{user}', [UserController::class, 'update'])->name('update');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        Route::post('/invite', [UserController::class, 'invite'])->name('invite');

        // Детальная информация пользователя (Relation Manager)
        Route::prefix('/{user}/detail')->name('detail.')->group(function () {
            Route::get('/create', [UserDetailController::class, 'create'])->name('create');
            Route::post('/', [UserDetailController::class, 'store'])->name('store');
            Route::get('/{userDetail}/edit', [UserDetailController::class, 'edit'])->name('edit');
            Route::put('/{userDetail}', [UserDetailController::class, 'update'])->name('update');
            Route::delete('/{userDetail}', [UserDetailController::class, 'destroy'])->name('destroy');
        });
    });

    // CRUD главных разделов
    Route::prefix('/dashboard/main-sections')->name('dashboard.main-sections.')->group(function () {
        Route::get('/', [MainSectionController::class, 'index'])->name('index');
        Route::get('/create', [MainSectionController::class, 'create'])->name('create');
        Route::post('/', [MainSectionController::class, 'store'])->name('store');
        Route::get('/{mainSection}/edit', [MainSectionController::class, 'edit'])->name('edit');
        Route::put('/{mainSection}', [MainSectionController::class, 'update'])->name('update');
        Route::delete('/{mainSection}', [MainSectionController::class, 'destroy'])->name('destroy');
    });

    // CRUD подразделов
    Route::prefix('/dashboard/sub-sections')->name('dashboard.sub-sections.')->group(function () {
        Route::get('/', [SubSectionController::class, 'index'])->name('index');
        Route::get('/create', [SubSectionController::class, 'create'])->name('create');
        Route::post('/', [SubSectionController::class, 'store'])->name('store');
        Route::get('/{subSection}/edit', [SubSectionController::class, 'edit'])->name('edit');
        Route::put('/{subSection}', [SubSectionController::class, 'update'])->name('update');
        Route::delete('/{subSection}', [SubSectionController::class, 'destroy'])->name('destroy');

        // Управление привязкой к главным разделам
        Route::post('/{subSection}/attach-to-main-section', [SubSectionController::class, 'attachToMainSection'])->name('attach-to-main-section');
        Route::post('/{subSection}/detach-from-main-section', [SubSectionController::class, 'detachFromMainSection'])->name('detach-from-main-section');

        // Управление страницами (Relation Manager)
        Route::post('/{subSection}/pages/attach', [SubSectionController::class, 'attachPage'])->name('pages.attach');
        Route::delete('/{subSection}/pages/{page}/detach', [SubSectionController::class, 'detachPage'])->name('pages.detach');
    });

    // CRUD страниц
    Route::prefix('/dashboard/pages')->name('dashboard.pages.')->group(function () {
        Route::get('/', [PageController::class, 'index'])->name('index');
        Route::get('/create', [PageController::class, 'create'])->name('create');
        Route::post('/', [PageController::class, 'store'])->name('store');
        Route::get('/{page}/edit', [PageController::class, 'edit'])->name('edit');
        Route::put('/{page}', [PageController::class, 'update'])->name('update');
        Route::delete('/{page}', [PageController::class, 'destroy'])->name('destroy');
    });

    // CRUD контактных виджетов
    Route::prefix('/dashboard/contact-widgets')->name('dashboard.contact-widgets.')->group(function () {
        Route::get('/', [ContactWidgetController::class, 'index'])->name('index');
        Route::get('/create', [ContactWidgetController::class, 'create'])->name('create');
        Route::post('/', [ContactWidgetController::class, 'store'])->name('store');
        Route::get('/{contactWidget}/edit', [ContactWidgetController::class, 'edit'])->name('edit');
        Route::put('/{contactWidget}', [ContactWidgetController::class, 'update'])->name('update');
        Route::delete('/{contactWidget}', [ContactWidgetController::class, 'destroy'])->name('destroy');
    });

    // CRUD пользовательских форм
    Route::prefix('/dashboard/custom-forms')->name('dashboard.custom-forms.')->group(function () {
        Route::get('/', [CustomFormController::class, 'index'])->name('index');
        Route::get('/create', [CustomFormController::class, 'create'])->name('create');
        Route::post('/', [CustomFormController::class, 'store'])->name('store');
        Route::get('/{customForm}/edit', [CustomFormController::class, 'edit'])->name('edit');
        Route::put('/{customForm}', [CustomFormController::class, 'update'])->name('update');
        Route::delete('/{customForm}', [CustomFormController::class, 'destroy'])->name('destroy');

        // Ответы на форму (Relation Manager)
        Route::prefix('/{customForm}/responses')->name('responses.')->group(function () {
            Route::get('/', [CustomFormController::class, 'responses'])->name('index');
            Route::post('/{response}/toggle-checked', [CustomFormController::class, 'toggleResponseChecked'])->name('toggle-checked');
            Route::delete('/{response}', [CustomFormController::class, 'destroyResponse'])->name('destroy');
        });
    });

    // CRUD списков ресурсов
    Route::prefix('/dashboard/page-reference-lists')->name('dashboard.page-reference-lists.')->group(function () {
        Route::get('/', [PageReferenceListController::class, 'index'])->name('index');
        Route::get('/create', [PageReferenceListController::class, 'create'])->name('create');
        Route::post('/', [PageReferenceListController::class, 'store'])->name('store');
        Route::get('/{pageReferenceList}/edit', [PageReferenceListController::class, 'edit'])->name('edit');
        Route::put('/{pageReferenceList}', [PageReferenceListController::class, 'update'])->name('update');
        Route::delete('/{pageReferenceList}', [PageReferenceListController::class, 'destroy'])->name('destroy');
    });
});



