<?php

namespace App\Filament\Resources\SlideResource\Pages;

use App\Filament\Resources\SlideResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateSlide extends CreateRecord
{
    protected static string $resource = SlideResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['model_select'], $data['model']);

        return $data;
    }
}
