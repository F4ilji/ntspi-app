<?php

namespace App\Http\Controllers;

use App\Http\Resources\ClientContactWidgetResource;
use App\Http\Resources\ClientPageReferenceListResource;
use App\Models\ContactWidget;

class ClientWidgetContactController extends Controller
{
    public function show(string $slug)
    {
        return new ClientContactWidgetResource(ContactWidget::query()->where('slug', $slug)->first());
    }
}
