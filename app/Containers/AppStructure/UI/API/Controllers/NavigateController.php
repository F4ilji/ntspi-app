<?php

namespace App\Containers\AppStructure\UI\API\Controllers;

use App\Containers\AppStructure\Tasks\GetNavigationDataTask;
use App\Containers\AppStructure\UI\API\Transformers\NavigationResource;
use App\Ship\Controllers\Controller;

class NavigateController extends Controller
{
    public function __construct(private readonly GetNavigationDataTask $getNavigationDataTask){}

    public function index()
    {
        return NavigationResource::collection($this->getNavigationDataTask->run());
    }
}
