<?php

namespace App\Containers\AdditionalEducation\UI\WEB\Controllers;

use App\Containers\AdditionalEducation\Data\Resources\AdditionalEducationCategoryPreviewResource;
use App\Containers\AdditionalEducation\Data\Resources\AdditionalEducationCategoryResource;
use App\Containers\AdditionalEducation\Data\Resources\AdditionalEducationResource;
use App\Containers\AdditionalEducation\Data\Resources\DirectionAdditionalEducationResource;
use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Containers\AdditionalEducation\Models\DirectionAdditionalEducation;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use App\Ship\Enums\Education\FormEducation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientAdditionalEducationController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}


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
                    ->withActivePrograms()
                    ->where('is_active', true);

                if ($request->has('form')) {
                    $formValue = FormEducation::fromName($request->form)->value;
                    $query->whereHas('additionalEducations', fn ($q) => $q->where('form_education', $formValue))
                        ->with(['additionalEducations' => fn ($q) => $q->where('form_education', $formValue)]);
                }

                if ($request->has('category')) {
                    $slugs = is_array($request->category) ? $request->category : [$request->category];
                    $query->whereIn('slug', $slugs);
                }

                if ($request->has('direction')) {
                    $query->whereHas('direction', fn($q) => $q->where('slug', $request->direction));
                }

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

        $settingsPage = request()->attributes->get('settings_page') ?? [];

        if (array_key_exists('custom_form', $settingsPage)) {
            $form = $settingsPage['custom_form'];
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
