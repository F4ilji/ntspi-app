<?php

namespace App\Containers\InstituteStructure\UI\WEB\Controllers;

use App\Containers\InstituteStructure\Actions\FindDivisionBySlugAction;
use App\Containers\InstituteStructure\Actions\ListDivisionsAction;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DivisionResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use Inertia\Inertia;

class ClientDivisionController extends Controller
{
    public function __construct(
        readonly SeoServiceInterface $seoPageProvider,
        private readonly ListDivisionsAction $listDivisionsAction,
        private readonly FindDivisionBySlugAction $findDivisionBySlugAction
    ) {
    }

    public function index(): \Inertia\Response
    {
        $divisions = $this->listDivisionsAction->run();

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/Divisions/Index', compact('divisions', 'seo'));
    }

    public function show(string $slug): \Inertia\Response
    {
        $divisions = $this->listDivisionsAction->run();

        $divisionData = $this->findDivisionBySlugAction->run($slug);

        $seo = $this->seoPageProvider->getSeoForModel($divisionData);

        $division = new DivisionResource($divisionData);

        return Inertia::render('Client/Divisions/Show', compact('divisions', 'division', 'seo'));
    }
}
