<?php

namespace App\Filament\Resources\AdditionalEducationResource\Pages;

use App\Filament\Resources\AdditionalEducationResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateAdditionalEducation extends CreateRecord
{
    protected static string $resource = AdditionalEducationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->seoData = $this->generateSeo($data);

        return $data;
    }

    protected function afterCreate(): void
    {
        $this->record->seo()->create($this->seoData);
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

    private function getBlockBySeoActiveState(string $name, array $content) : array|null
    {
        $data = [];
        foreach ($content as $block) {
            if ($block['type'] === $name) {
                $data[] = $block;
            }
        }
        $block = null;
        foreach ($data as $item) {
            if ($item['data']['seo_active'] === true) {
                $block = $item;
            }
        }
        return $block;
    }

    private function getDataFromBlocks($block) : string
    {
        $data = "";
        switch ($block['type']) {
            case 'paragraph':
                $data .= strip_tags($block['data']['content']) . " ";
                break;
            case 'heading':
                $data .= strip_tags($block['data']['content']) . " ";
                break;
            case 'files':
                foreach ($block['data']['file'] as $file) {
                    $data .= $file['title'] . " ";
                }
                break;
            case 'person':
                $data .= $block['data']['name'] . " ";
                break;
            case 'stepper':
                $data .= $block['data']['step_name'] . " ";
                foreach ($block['data']['steps'] as $step) {
                    $data .= $step['title'] . " ";
                    $data .= strip_tags($step['content']) . " ";
                }
                break;
            case 'tabs':
                foreach ($block['data']['tab'] as $item) {
                    foreach ($item['content'] as $block) {
                        $data .= $this->getDataFromBlocks($block);
                    };
                };
                break;

        }
        return $data;
    }
}
