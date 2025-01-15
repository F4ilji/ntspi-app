<?php

namespace App\Http\Middleware;

use App\Http\Resources\ClientBreadcrumbPage;
use App\Http\Resources\ClientBreadcrumbSection;
use App\Http\Resources\ClientBreadcrumbSubSection;
use App\Models\Page;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

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
