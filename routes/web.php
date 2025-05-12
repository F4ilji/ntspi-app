<?php

use App\Containers\AppStructure\UI\WEB\Controllers\PageController;
use App\Http\Controllers\MainController;
use Illuminate\Support\Facades\Route;



Route::middleware('access-check')->group(function () {

    // Главная страница
    Route::get('/', [MainController::class, 'index'])->name('index');

    Route::get('{path}', [PageController::class, 'render'])->where('path', '[0-9,a-z,/,-]+')->name('page.view');


//    // Расписание занятий
//    Route::get('/schedule', [ClientScheduleController::class, 'index'])->name('client.schedule.index');
//    Route::get('/schedule/{id}', [ClientScheduleController::class, 'show'])->name('client.schedule.show');

//    Route::get('/persons/{slug}', [PersonController::class, 'show'])->name('client.person.show');

    // Новости
//    Route::get('/news', [ClientPostController::class, 'index'])->name('client.post.index');
//    Route::get('/news/{slug}', [ClientPostController::class, 'show'])->name('client.post.show');

    // Образовательные программы

//    Route::get('/programs/', [ClientProgramController::class, 'index'])->name('client.program.index');
//    Route::get('/program/{slug}', [ClientProgramController::class, 'show'])->name('client.program.show');


    // Образовательные программы
//    Route::get('/additional-education/', [ClientAdditionalEducationController::class, 'index'])->name('client.additionalEducation.index');
//    Route::get('/additional-education/{slug}', [ClientAdditionalEducationController::class, 'show'])->name('client.additionalEducation.show');

    // События
//    Route::get('/events', [ClientEventController::class, 'index'])->name('client.event.index');
//    Route::get('/events/archive', [ClientEventController::class, 'archive'])->name('client.event.archive'); // Доделать builder
//    Route::get('/events/{slug}', [ClientEventController::class, 'show'])->name('client.event.show');

//    // Заметки библиотеки
//    Route::get('/library/news', [ClientLibraryNewsController::class, 'index'])->name('client.library.news.index'); // Доделать builder
//    Route::get('/library/news/{slug}', [ClientLibraryNewsController::class, 'show'])->name('client.library.news.show');
//
//    // Виртуальные выставки библиотеки
//    Route::get('/library/exhibition', [ClientVirtualExhibitionController::class, 'index'])->name('client.library.exhibition.index'); // Доделать builder
//    Route::get('/library/exhibition/{slug}', [ClientVirtualExhibitionController::class, 'show'])->name('client.library.exhibition.show');

    // Вакансии вуза
//    Route::get('/vacant/', [ClientVacantPositionController::class, 'index'])->name('client.vacant.index');

//    // Вакансии других учереждений
//    Route::get('/current-vacancies/', [ClientExternalVacancyController::class, 'index'])->name('client.external.vacant.index'); // Доделать builder
//    Route::get('/current-vacancies/{id}', [ClientExternalVacancyController::class, 'show'])->name('client.external.vacant.show');


//    Route::get('/academic-journals/', [ClientAcademicJournalController::class, 'index'])->name('client.academicJournals.index');
//    Route::get('/academic-journals/{slug}', [ClientAcademicJournalController::class, 'show'])->name('client.academicJournals.show');

//    // Факультеты и кафедры
//    Route::get('/faculties', [ClientFacultyController::class, 'index'])->name('client.faculty.index');
//    Route::get('/faculties/{slug}', [ClientFacultyController::class, 'show'])->name('client.faculty.show');
//    Route::get('/faculties/{facultySlug}/{departmentSlug}', [ClientDepartmentController::class, 'show'])->name('client.department.show');
//
//    // Подразделения института
//    Route::get('/divisions', [ClientDivisionController::class, 'index'])->name('client.division.index');
//    Route::get('/divisions/{slug}', [ClientDivisionController::class, 'show'])->name('client.division.show');

});


