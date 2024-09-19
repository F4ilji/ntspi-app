<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\SubSection;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $subSection = SubSection::find($data['sub_section_id']);


        if ($subSection == null) {
            $data['path'] = $data['slug'];
        } elseif($subSection->mainSection == null) {
            $data['path'] =  $subSection->slug . '/' . $data['slug'];
        } else {
            $data['path'] = $subSection->mainSection->slug . '/' . $subSection->slug . '/' . $data['slug'];
        }
        unset($data['sub_section_id']);




        $result = "";
        foreach ($data['content'] as $block) {
            $result .= $this->getDataFromBlocks($block);
        }

        // Удаляем лишние пробелы и переносы строк
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);


        // Приводим текст к нижнему регистру
        $data['search_data'] = strtolower($result);

        return $data;
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
