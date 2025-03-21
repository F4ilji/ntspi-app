<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use App\Models\SubSection;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreatePage extends CreateRecord
{
    protected static string $resource = PageResource::class;

    protected array $seoData;

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

        $this->seoData = $this->generateSeo($data);

        $data['search_data'] = $this->generateSearchData($data['content']);

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
        if ($rowData !== null) {
            $description = strip_tags($rowData['data']['content']);
        } else {
            $description = null;
        }
        return [
            'title' => $title,
            'description' => Str::limit(htmlspecialchars($description, ENT_QUOTES, 'UTF-8'), 160),
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

    private function generateSearchData(array $data) : string
    {
        $result = "";
        foreach ($data as $block) {
            $result .= $this->getDataFromBlocks($block);
        }
        // Удаляем лишние пробелы и переносы строк
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return strtolower($result);
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
