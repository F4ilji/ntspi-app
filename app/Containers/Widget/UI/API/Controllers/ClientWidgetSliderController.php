<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\GetSliderBySlugAction;
use App\Ship\Controllers\Controller;

class ClientWidgetSliderController extends Controller
{
    public function __construct(
        private readonly GetSliderBySlugAction $getSliderBySlugAction
    ) {}

    public function show(string $slug): ?object
    {
        return $this->getSliderBySlugAction->run($slug);
    }
}
