<?php

namespace App\Containers\Education\UI\WEB\Controllers;

use App\Containers\Education\Actions\ListEducationalProgramsAction;
use App\Containers\Education\Actions\GetEducationalProgramAction;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientProgramController extends Controller
{
    public function __construct(
        private readonly ListEducationalProgramsAction $listEducationalProgramsAction,
        private readonly GetEducationalProgramAction $getEducationalProgramAction,
    ) {}

    public function index(Request $request): \Inertia\Response
    {
        $filters = $request->only(['search', 'category', 'tag', 'sort', 'page', 'date', 'level', 'form', 'budget', 'direction']);

        $data = $this->listEducationalProgramsAction->run($filters);

        return Inertia::render('Client/Programs/Index', $data);
    }

    public function show(string $slug): \Inertia\Response
    {
        $data = $this->getEducationalProgramAction->run($slug);

        return Inertia::render('Client/Programs/Show', $data);
    }
}
