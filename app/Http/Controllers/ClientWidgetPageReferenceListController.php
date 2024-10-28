<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientPageReferenceListResource;
use App\Models\PageReferenceList;
use Illuminate\Http\Request;

class ClientWidgetPageReferenceListController extends Controller
{
    public function show(string $slug)
    {
        return new ClientPageReferenceListResource(PageReferenceList::query()->where('slug', $slug)->first());
    }
}
