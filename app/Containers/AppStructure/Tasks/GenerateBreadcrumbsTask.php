<?php

namespace App\Containers\AppStructure\Tasks;

use App\Containers\AppStructure\Models\Page;
use App\Containers\AppStructure\Tasks\FindPageByPathTask;
use App\Containers\AppStructure\Tasks\GeneratePathTask;
use App\Containers\AppStructure\Tasks\GetIndexRouteNameTask;
use App\Ship\Resources\Breadcrumb\ClientBreadcrumbPage;
use App\Ship\Resources\Breadcrumb\ClientBreadcrumbSection;
use App\Ship\Resources\Breadcrumb\ClientBreadcrumbSubSection;
use Illuminate\Support\Facades\Route;

class GenerateBreadcrumbsTask
{
    public function __construct(
        private readonly GeneratePathTask $generatePathTask,
        private readonly GetIndexRouteNameTask $getIndexRouteNameTask,
        private readonly FindPageByPathTask $findPageByPathTask
    ){}

    public function run(?string $routeN = null): ?array
    {
        $routeName = $routeN ?? Route::currentRouteName();

        $indexRouteName = $this->getIndexRouteNameTask->run($routeName);

        $finalRouteName = Route::has($indexRouteName) ? $indexRouteName : $routeName;

        $path = $this->generatePathTask->run($finalRouteName);

        if ($path === null) {
            return null;
        }

        $page = $this->findPageByPathTask->run($path);

        if (!$page?->section) {
            return null;
        }

        return [
            'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
            'subSection' => new ClientBreadcrumbSubSection($page->section),
            'page' => new ClientBreadcrumbPage($page),
        ];
    }
}
