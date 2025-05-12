<?php

namespace App\Containers\InstituteStructure\UI\WEB\Controllers;

use App\Containers\InstituteStructure\Models\Division;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DivisionResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class ClientDivisionController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function index()
    {
        $divisions = Cache::remember(CacheKeys::DIVISIONS_PREFIX->value . 'list', now()->addDay(), function () {
            return DivisionResource::collection(
                Division::query()->where('is_active', true)->get()
            );
        });

        $seo = Cache::remember(CacheKeys::DIVISIONS_PREFIX->value . 'seo', now()->addDay(), function () {
            return $this->seoPageProvider->getSeoForCurrentPage();
        });

        return Inertia::render('Client/Divisions/Index', compact('divisions', 'seo'));
    }

    public function show(string $slug): \Inertia\Response
    {
        $divisions = Cache::remember(CacheKeys::DIVISIONS_PREFIX->value . 'list', now()->addDay(), function () {
            return DivisionResource::collection(
                Division::query()->where('is_active', true)->get()
            );
        });

        $divisionData = Cache::remember(CacheKeys::DIVISION_PREFIX->value . $slug, now()->addDay(), function () use ($slug) {
            return Division::with(['workers.userDetail', 'seo'])->where('is_active', true)->where('slug', $slug)->firstOrFail();
        });

        $seo = $this->seoPageProvider->getSeoForModel($divisionData);

        $division = new DivisionResource($divisionData);

        return Inertia::render('Client/Divisions/Show', compact('divisions', 'division', 'seo'));
    }

}
