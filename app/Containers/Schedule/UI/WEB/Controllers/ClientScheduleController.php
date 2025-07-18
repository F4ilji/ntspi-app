<?php

namespace App\Containers\Schedule\UI\WEB\Controllers;

use App\Containers\Schedule\Actions\ListSchedulesAction;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;

class ClientScheduleController extends Controller
{
    public function __construct(
        private readonly ListSchedulesAction $listSchedulesAction,
    ) {}

    public function index(Request $request)
    {
        $filters = $request->only(['search', 'form', 'faculty', 'favorite']);

        $data = $this->listSchedulesAction->run($filters);

        return inertia()->render('Client/Schedules/Index', $data);
    }
}
