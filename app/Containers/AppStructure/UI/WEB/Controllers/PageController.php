<?php

namespace App\Containers\AppStructure\UI\WEB\Controllers;

use App\Containers\AppStructure\Actions\RenderPageAction;
use App\Ship\Controllers\Controller;
use Inertia\Response;

class PageController extends Controller
{
    public function __construct(private readonly RenderPageAction $renderPageAction){}

    public function render(string $path): Response
    {
        return $this->renderPageAction->run($path);
    }
}
