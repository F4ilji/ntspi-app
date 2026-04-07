<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class CreateSliderController extends Controller
{
    public function __invoke(): \Inertia\Response
    {
        return Inertia::render('Dashboard/Sliders/Create');
    }
}
