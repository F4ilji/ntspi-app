<?php

namespace App\Filament\Resources\CustomFormResponseResource\Pages;

use App\Filament\Resources\CustomFormResponseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCustomFormResponse extends EditRecord
{
    protected static string $resource = CustomFormResponseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
