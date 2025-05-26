<?php

namespace App\Containers\InstituteStructure\UI\WEB\Controllers;


use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\InstituteStructure\UI\WEB\Transformers\FacultyPreviewResource;
use App\Containers\InstituteStructure\UI\WEB\Transformers\FacultyResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;


class ClientFacultyController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}


    public function index(Request $request)
    {
        $faculties = Cache::remember(
            CacheKeys::FACULTIES_PREFIX->value . 'active_list',
            now()->addDay(), // Кешируем на 1 день
            function () {
                return FacultyPreviewResource::collection(
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
                return FacultyPreviewResource::collection(
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
                    ->with(['departments.faculty',
                        'workers' => fn ($query) => $query->orderBy('sort', 'asc'),
                        'workers.userDetail',
                        'seo'
                    ])
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

        $faculty = new FacultyResource($faculty);



        return Inertia::render('Client/Faculties/Show', compact('faculty', 'faculties', 'seo'));
    }
}
