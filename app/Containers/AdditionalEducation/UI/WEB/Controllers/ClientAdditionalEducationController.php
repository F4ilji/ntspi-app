<?php

namespace App\Containers\AdditionalEducation\UI\WEB\Controllers;

use App\Containers\AdditionalEducation\Actions\GetAllAdditionalEducationsAction;
use App\Containers\AdditionalEducation\Actions\GetAdditionalEducationBySlugAction;
use App\Ship\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientAdditionalEducationController extends Controller
{
    public function __construct(
        readonly GetAllAdditionalEducationsAction $getAllAdditionalEducationsAction,
        readonly GetAdditionalEducationBySlugAction $getAdditionalEducationBySlugAction
    ){}


    public function index(Request $request): \Inertia\Response
    {
        $data = $this->getAllAdditionalEducationsAction->run($request);

        return Inertia::render('Client/Additional-educations/Index', $data);
    }

    public function show(string $slug, Request $request): \Inertia\Response
    {
        $data = $this->getAdditionalEducationBySlugAction->run($slug, $request);

        return Inertia::render('Client/Additional-educations/Show', $data);
    }
}
