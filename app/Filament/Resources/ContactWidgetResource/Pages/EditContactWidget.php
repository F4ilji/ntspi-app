<?php

namespace App\Filament\Resources\ContactWidgetResource\Pages;

use App\Filament\Resources\ContactWidgetResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContactWidget extends EditRecord
{
    protected static string $resource = ContactWidgetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
