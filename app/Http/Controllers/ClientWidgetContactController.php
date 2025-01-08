<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientPageReferenceListResource;
use App\Models\ContactWidget;
use App\Models\PageReferenceList;
use Illuminate\Http\Request;

class ClientWidgetContactController extends Controller
{
    public function show(string $slug)
    {
        return new ClientPageReferenceListResource(ContactWidget::query()->where('slug', $slug)->first());
    }
}
