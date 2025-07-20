<?php

namespace App\Containers\InstituteStructure\UI\WEB\Controllers;

use App\Containers\InstituteStructure\Actions\FindFacultyBySlugAction;
use App\Containers\InstituteStructure\Actions\ListFacultiesAction;
use App\Containers\InstituteStructure\UI\WEB\Transformers\FacultyResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use Inertia\Inertia;

class ClientFacultyController extends Controller
{
    public function __construct(
        readonly SeoServiceInterface $seoPageProvider,
        private readonly ListFacultiesAction $listFacultiesAction,
        private readonly FindFacultyBySlugAction $findFacultyBySlugAction
    ) {
    }

    public function index(): \Inertia\Response
    {
        $faculties = $this->listFacultiesAction->run();

        $seo = $this->seoPageProvider->getSeoForCurrentPage();

        return Inertia::render('Client/Faculties/Index', compact('faculties', 'seo'));
    }

    public function show(string $slug): \Inertia\Response
    {
        $faculties = $this->listFacultiesAction->run();

        $facultyData = $this->findFacultyBySlugAction->run($slug);

        $seo = $this->seoPageProvider->getSeoForModel($facultyData);

        $faculty = new FacultyResource($facultyData);

        return Inertia::render('Client/Faculties/Show', compact('faculty', 'faculties', 'seo'));
    }
}
