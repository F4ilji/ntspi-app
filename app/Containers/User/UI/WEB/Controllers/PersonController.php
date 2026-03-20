<?php

namespace App\Containers\User\UI\WEB\Controllers;

use App\Containers\User\Actions\GetPersonDataAction;
use App\Containers\User\Models\User;
use App\Containers\User\UI\WEB\Transformers\FullInfoPersonResource;
use App\Ship\Controllers\Controller;
use Inertia\Inertia;

class PersonController extends Controller
{
    public function __construct(private readonly GetPersonDataAction $getPersonDataAction){}

    public function show(string $slug)
    {
        $data = $this->getPersonDataAction->run($slug);

        $person = new FullInfoPersonResource($data['personData']);
        $seo = $data['seo'];

        return Inertia::render('Client/Persons/Show', compact('person', 'seo'));
    }
}
