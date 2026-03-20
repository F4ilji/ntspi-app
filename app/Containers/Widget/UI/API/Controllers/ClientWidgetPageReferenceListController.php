<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\GetPageReferenceListPageAction;
use App\Ship\Controllers\Controller;

class ClientWidgetPageReferenceListController extends Controller
{
    public function __construct(private GetPageReferenceListPageAction $getPageReferenceListPageAction)
    {
    }

    public function show(string $slug)
    {
        return $this->getPageReferenceListPageAction->run($slug);
    }
}
