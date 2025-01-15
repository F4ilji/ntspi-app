<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientPageNavigateResource;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClientWidgetPageController extends Controller
{
    public function single(int $id)
    {
        $cacheKey = 'page_' . md5($id);

        // Пытаемся получить данные из кеша
        $page = Cache::remember($cacheKey, now()->addHours(1), function () use ($id) {
            return Page::where('id', '=', $id)
                ->with('section.pages.section', 'section.mainSection')
                ->first();
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
                'page' => new ClientPageNavigateResource($page),
                'breadcrumbs' => $breadcrumbs
            ],
        ], 200);
    }
}
