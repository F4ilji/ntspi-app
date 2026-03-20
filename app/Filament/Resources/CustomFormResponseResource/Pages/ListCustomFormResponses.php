<?php

namespace App\Filament\Resources\CustomFormResponseResource\Pages;

use App\Filament\Resources\CustomFormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCustomFormResponses extends ListRecords
{
    protected static string $resource = CustomFormResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
