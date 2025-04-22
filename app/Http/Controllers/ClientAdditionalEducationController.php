<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Enums\FormEducation;
use App\Http\Resources\AdditionalEducationCategoryPreviewResource;
use App\Http\Resources\AdditionalEducationCategoryResource;
use App\Http\Resources\AdditionalEducationResource;
use App\Http\Resources\ClientBreadcrumbPage;
use App\Http\Resources\ClientBreadcrumbSection;
use App\Http\Resources\ClientBreadcrumbSubSection;
use App\Http\Resources\DirectionAdditionalEducationResource;
use App\Models\AdditionalEducation;
use App\Models\AdditionalEducationCategory;
use App\Models\DirectionAdditionalEducation;
use App\Models\Page;
use App\Services\App\Breadcrumb\BreadcrumbService;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientAdditionalEducationController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}


    public function index(Request $request): \Inertia\Response
    {
        $cacheKey = md5(serialize($request->all()));

        // Основные данные (кешируются)
        $directionAdditionalEducations = Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . 'directions_' . $cacheKey,
            now()->addDay(),
            function () {
                return DirectionAdditionalEducationResource::collection(
                    DirectionAdditionalEducation::query()
                        ->where('is_active', true)
                        ->whereHas('additionalEducationCategories', fn ($q) => $q->whereHas('additionalEducations'))
                        ->get()
                );
            }
        );

        $additionalEducations = Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . $cacheKey,
            now()->addDay(),
            function () use ($request) {
                $query = AdditionalEducationCategory::query()
                    ->has('additionalEducations')
                    ->WithActivePrograms()
                    ->where('is_active', true)
                    ->when($request->direction, function ($q, $direction) use ($request) {
                        return $q->whereHas('direction', fn($query) => $query->where('slug', $direction))
                            ->when($request->form, fn($query, $form) => $query->whereHas('additionalEducations', fn($q) => $q->where('form_education', FormEducation::fromName($form))
                            )
                                ->when($request->category, fn($query) => is_array($request->category)
                                    ? $query->whereIn('slug', $request->category)
                                    : $query->where('slug', $request->category)
                                )
                                ->has('additionalEducations'));
                    });

                return AdditionalEducationCategoryResource::collection($query->get());
            }
        );

        $categories = Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . 'categories',
            now()->addWeek(),
            function () {
                return AdditionalEducationCategoryPreviewResource::collection(
                    AdditionalEducationCategory::query()
                        ->where('is_active', true)
                        ->has('additionalEducations')
                        ->get()
                );
            }
        );

        // Динамические данные (не кешируются)
        $categoriesContent = [];
        if ($request->category) {
            foreach ((array)$request->category as $item) {
                $categoriesContent[$item] = new AdditionalEducationCategoryResource(
                    AdditionalEducationCategory::where('slug', $item)->first()
                );
            }
        }

        $forms_education = array_reduce(
            FormEducation::cases(),
            fn ($acc, $case) => $acc + [$case->name => $case->getLabel()],
            []
        );

        $filters = [
            'direction_filter' => [
                'type' => 'direction',
                'value' => $request->input('direction'),
                'param' => 'direction'
            ],
            'form_education_filter' => [
                'type' => 'form',
                'value' => $request->input('form'),
                'param' => 'form'
            ],
            'category_filter' => [
                'type' => 'category',
                'value' => $request->input('category'),
                'param' => 'category',
                'content' => $categoriesContent,
            ],
        ];

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/Additional-educations/Index', compact(
            'directionAdditionalEducations',
            'additionalEducations',
            'filters',
            'forms_education',
            'categories',
            'seo'
        ));
    }

    public function show(string $slug): \Inertia\Response
    {
        $additionalEducationModel = Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAM_PREFIX->value . $slug,
            now()->addDay(),
            fn() => AdditionalEducation::with('category.direction')->where('slug', $slug)->firstOrFail()
        );

        $seo = Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAM_PREFIX->value . 'seo_' . $slug,
            now()->addDay(),
            fn() => $this->seoPageProvider->getSeoForModel($additionalEducationModel)
        );

        if (array_key_exists('custom_form', request()->input('settings_page'))) {
            $form = request()->input('settings_page')['custom_form'];
        } else {
            $form = null;
        }

        $additionalEducation = new AdditionalEducationResource($additionalEducationModel);


        return Inertia::render('Client/Additional-educations/Show', compact(
            'additionalEducation',
            'seo',
            'form',
        ));
    }}
