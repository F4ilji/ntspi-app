<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\AppStructure\Models\Page;
use App\Containers\Widget\UI\API\Transformers\PageNavigateResource;
use App\Ship\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class ClientWidgetPageController extends Controller
{
    public function single(int $id): \Illuminate\Http\JsonResponse
    {
        $cacheKey = 'page_' . md5($id);

        // Пытаемся получить данные из кеша
        $page = Cache::remember($cacheKey, now()->addHours(1), function () use ($id) {
            return Page::where('id', '=', $id)
                ->with('section.pages.section', 'section.mainSection')
                ->firstOrFail();
        });

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => $page->section->mainSection->title,
                'subSection' => $page->section->title,
                'page' => $page->title,
            ];
        } else {
            $breadcrumbs = null;
        }
        return response()->json([
            'data' => [
                'page' => new PageNavigateResource($page),
                'breadcrumbs' => $breadcrumbs
            ],
        ], 200);
    }
}
