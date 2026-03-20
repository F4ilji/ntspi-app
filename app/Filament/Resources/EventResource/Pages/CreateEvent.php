<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Services\Filament\Domain\Seo\SeoGeneratorService;
use App\Services\Filament\Traits\SeoGenerate;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    use SeoGenerate;

    protected static string $resource = EventResource::class;

//    protected function mutateFormDataBeforeCreate(array $data): array
//    {
//    }


    protected function afterCreate(): void
    {
        $this->createSeo($this->record);
    }


}
