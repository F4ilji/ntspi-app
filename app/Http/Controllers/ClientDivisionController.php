<?php

namespace App\Http\Controllers;

use App\Http\Resources\DivisionResource;
use App\Models\Division;
use App\Services\App\Breadcrumb\BreadcrumbService;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientDivisionController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

    public function index()
    {
        $divisions = DivisionResource::collection(Division::query()->where('is_active', true)->get());

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/Divisions/Index', compact('divisions', 'seo'));
    }

    public function show(string $slug)
    {
        $divisions = DivisionResource::collection(Division::query()->where('is_active', true)->get());
        $division = new DivisionResource($divisionModel = Division::with(['workers.userDetail', 'seo'])->where('is_active', true)->where('slug', $slug)->firstOrFail());

        $seo = $this->seoPageProvider->getSeoForModel($divisionModel);

        return Inertia::render('Client/Divisions/Show', compact('divisions', 'division', 'seo'));
    }
}
