<?php

namespace App\Containers\Dashboard\UI\WEB\Controllers;

use App\Containers\Dashboard\Actions\LoadDashboardDataAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexDashboardController extends Controller
{
    public function __construct(
        private readonly LoadDashboardDataAction $loadDashboardDataAction,
    ) {}

    public function __invoke(Request $request): \Inertia\Response
    {
        return inertia()->render('Dashboard/Main', $this->loadDashboardDataAction->run());
    }
}