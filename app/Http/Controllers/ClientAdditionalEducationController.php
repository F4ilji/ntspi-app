<?php

namespace App\Http\Controllers;

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
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientAdditionalEducationController extends Controller
{
    public function index(Request $request)
    {

        $directionAdditionalEducations = DirectionAdditionalEducationResource::collection(
            DirectionAdditionalEducation::query()
                ->where('is_active', true)
                ->whereHas('additionalEducationCategories', function ($q) {
                    $q->whereHas('additionalEducations');
            })->get());

        $additionalEducations = AdditionalEducationCategoryResource::collection(AdditionalEducationCategory::query()
            ->WithActivePrograms()
            ->where('is_active', '=', true)
            ->when($request->input('direction'), function ($q, $direction) {
                $q->whereHas('direction', function ($query) use ($direction) {
                    $query->where('slug', $direction);
                });
            })
            ->when(request()->input('form'), function ($query, $form) {
                $query->whereHas('additionalEducations', function ($q) use ($form) {
                    $q->where('form_education', FormEducation::fromName($form));
                });
                $query->with(['additionalEducations' => function ($q) use ($form) {
                    $q->where('form_education', FormEducation::fromName($form));
                }]);
            })
            ->when(request()->input('category'), function ($query) {
                $slugs = request()->input('category');
                if (is_array($slugs)) {
                    $query->whereIn('slug', $slugs);
                }
            })
            ->has('additionalEducations')
            ->get());

        $categories = AdditionalEducationCategoryPreviewResource::collection(
            AdditionalEducationCategory::query()
                ->where('is_active', true)
                ->has('additionalEducations')
                ->get()
        );
        $categoriesContent = [];
        if (request()->input('category')) {
            foreach (request()->input('category') as $item) {
                $categoriesContent[$item] = new AdditionalEducationCategoryResource(AdditionalEducationCategory::where('slug', $item)->first());
            }
        }

        $forms_education = [];
        foreach (FormEducation::cases() as $case) {
            $forms_education[$case->name] = $case->getLabel();
        }
        $filters = [
            'direction_filter' => [
                'type' => 'direction',
                'value' => request()->input('direction'),
                'param' => 'direction'
            ],
            'form_education_filter' => [
                'type' => 'form',
                'value' => request()->input('form'),
                'param' => 'form'
            ],
            'category_filter' => [
                'type' => 'category',
                'value' => request()->input('category'),
                'param' => 'category',
                'content' => $categoriesContent,
            ],
        ];

        $routeUrl = route('client.additionalEducation.index');
        $path = ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');

        $page = Page::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }

        return Inertia::render('Client/Additional-educations/Index',
            compact(
                'directionAdditionalEducations',
                'additionalEducations',
                'filters',
                'forms_education',
                'categories',
                'breadcrumbs'
            ));
    }

    public function show(string $slug)
    {
        $additionalEducation = new AdditionalEducationResource(AdditionalEducation::query()->with('category.direction')->where('slug', $slug)->first());
        $routeUrl = route('client.additionalEducation.index');
        $path = ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');

        $page = Page::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }

        return Inertia::render('Client/Additional-educations/Show', compact('additionalEducation', 'breadcrumbs'));
    }
}
