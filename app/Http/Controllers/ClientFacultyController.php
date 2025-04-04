<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\FacultyResource;
use App\Http\Resources\FullFacultyResource;
use App\Models\Faculty;
use App\Services\App\Breadcrumb\BreadcrumbService;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientFacultyController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}


    public function index(Request $request)
    {
        $faculties = Cache::remember(
            CacheKeys::FACULTIES_PREFIX->value . 'active_list',
            now()->addDay(), // Кешируем на 1 день
            function () {
                return FacultyResource::collection(
                    Faculty::query()
                        ->where('is_active', true)
                        ->get()
                );
            }
        );

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/Faculties/Index', compact('faculties', 'seo'));
    }


    public function show(string $slug)
    {
        // Кешируем список факультетов
        $faculties = Cache::remember(
            CacheKeys::FACULTIES_PREFIX->value . 'active_list',
            now()->addDay(),
            function () {
                return FacultyResource::collection(
                    Faculty::query()
                        ->where('is_active', true)
                        ->get()
                );
            }
        );

        // Кешируем данные конкретного факультета
        $faculty = Cache::remember(
            CacheKeys::FACULTY_PREFIX->value . $slug,
            now()->addDay(),
            function () use ($slug) {
                return Faculty::where('slug', $slug)
                    ->where('is_active', true)
                    ->with(['departments.faculty', 'workers.userDetail', 'seo'])
                    ->firstOrFail();
            }
        );

        $seo = Cache::remember(
            CacheKeys::FACULTY_PREFIX->value . "SEO_" . $slug,
            now()->addDay(),
            function () use ($faculty) {
                return $this->seoPageProvider->getSeoForModel($faculty);
            }
        );

        $faculty = new FullFacultyResource($faculty);



        return Inertia::render('Client/Faculties/Show', compact('faculty', 'faculties', 'seo'));
    }
}
