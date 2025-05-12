<?php

namespace App\Containers\AppStructure\UI\API\Controllers;

use App\Containers\AppStructure\Models\MainSection;
use App\Containers\AppStructure\UI\API\Transformers\NavigationResource;
use App\Ship\Controllers\Controller;

class NavigateController extends Controller
{
    public function index()
    {
        return NavigationResource::collection(MainSection::with('subSections.pages')->orderBy('sort', 'asc')->get());
    }
}
