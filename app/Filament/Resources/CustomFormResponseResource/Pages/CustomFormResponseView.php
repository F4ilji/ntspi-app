<?php

namespace App\Filament\Resources\CustomFormResponseResource\Pages;

use App\Filament\Resources\CustomFormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class CustomFormResponseView extends ViewRecord
{
    protected static string $resource = CustomFormResponseResource::class;

    protected static string $view = 'filament.resources.posts.pages.view-post';

}
