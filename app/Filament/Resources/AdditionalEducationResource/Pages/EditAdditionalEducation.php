<?php

namespace App\Filament\Resources\AdditionalEducationResource\Pages;

use App\Filament\Resources\AdditionalEducationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditAdditionalEducation extends EditRecord
{
    protected static string $resource = AdditionalEducationResource::class;

    protected array $seoData;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->seoData = $this->generateSeo($data);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->seo()->update($this->seoData);
    }

    private function generateSeo(array $data) : array
    {
        $title = $data['title'];
        $rowData = $this->getFirstBlockByName('paragraph', $data['content']);
        $description = strip_tags($rowData['data']['content']);
//        $image = ($data['preview'] !== null) ? $data['preview'] : null;

        return [
            'title' => $title,
            'description' => Str::limit($description, 160),
            'image' => "",
        ];
    }


    private function getFirstBlockByName(string $name, array $content) : array|null
    {
        $data = null;
        foreach ($content as $block) {
            $data = ($block['type'] === $name) ? $block : null;
            break;
        }
        return $data;
    }



    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
