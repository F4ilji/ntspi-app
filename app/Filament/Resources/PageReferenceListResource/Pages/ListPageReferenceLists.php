<?php

namespace App\Filament\Resources\PageReferenceListResource\Pages;

use App\Filament\Resources\PageReferenceListResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPageReferenceLists extends ListRecords
{
    protected static string $resource = PageReferenceListResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
