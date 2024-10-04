<?php

namespace App\Filament\Resources\SubSectionResource\Pages;

use App\Filament\Resources\SubSectionResource;
use App\Models\Page;
use App\Models\SubSection;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateSubSection extends CreateRecord
{
    protected static string $resource = SubSectionResource::class;
}
