<?php

namespace App\Containers\InstituteStructure\UI\WEB\Controllers;

use App\Containers\InstituteStructure\Actions\FindDepartmentBySlugAction;
use App\Containers\InstituteStructure\UI\WEB\Transformers\DepartmentResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use Inertia\Inertia;

class ClientDepartmentController extends Controller
{
    public function __construct(
        readonly SeoServiceInterface $seoPageProvider,
        private readonly FindDepartmentBySlugAction $findDepartmentBySlugAction
    ) {
    }

    public function show(string $facultySlug, string $departmentSlug): \Inertia\Response
    {
        $data = $this->findDepartmentBySlugAction->run($facultySlug, $departmentSlug);

        $seo = $this->seoPageProvider->getSeoForModel($data['departmentModel']);

        $department = $data['department'];
        $departments = $data['departments'];
        $directions = $data['directions'];

        return Inertia::render('Client/Departments/Show', compact(
            'department',
            'departments',
            'directions',
            'seo',
        ));
    }
}
