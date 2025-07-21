<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Widget\Actions\GetContactWidgetPageAction;
use App\Ship\Controllers\Controller;

class ClientWidgetContactController extends Controller
{
    public function __construct(private GetContactWidgetPageAction $getContactWidgetPageAction)
    {
    }

    public function show(string $slug)
    {
        return $this->getContactWidgetPageAction->run($slug);
    }
}
