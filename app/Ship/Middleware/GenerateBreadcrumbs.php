<?php

namespace App\Ship\Middleware;

use App\Containers\AppStructure\Models\Page;
use App\Ship\Resources\Breadcrumb\ClientBreadcrumbPage;
use App\Ship\Resources\Breadcrumb\ClientBreadcrumbSection;
use App\Ship\Resources\Breadcrumb\ClientBreadcrumbSubSection;
use Closure;
use Illuminate\Http\Request;

class GenerateBreadcrumbs
{
    public function handle(Request $request, Closure $next)
    {
        $path = $request->path();
        $page = Page::where('path', '=', $path)
            ->with('section.pages.section', 'section.mainSection')
            ->first();

        if ($page && isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }

        $request->merge(['breadcrumbs' => $breadcrumbs]);

        return $next($request);
    }
}
