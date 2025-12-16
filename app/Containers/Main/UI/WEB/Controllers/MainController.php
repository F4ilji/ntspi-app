<?php

namespace App\Containers\Main\UI\WEB\Controllers;

use App\Containers\Main\Actions\GetMainPageDataAction;
use App\Ship\Controllers\Controller;
use Inertia\Inertia;

class MainController extends Controller
{
    public function __construct(
        private readonly GetMainPageDataAction $getMainPageDataAction
    ) {}

    public function index()
    {
        $data = $this->getMainPageDataAction->run();

        return Inertia::render('Main', $data);
    }
}