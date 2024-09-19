<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientPageNavigateResource;
use App\Http\Resources\PageResource;
use App\Models\Page;
use Illuminate\Http\Request;

class ClientWidgetPageController extends Controller
{
    public function single(int $id)
    {
        $page = Page::query()
            ->with('section')
            ->find($id);
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
