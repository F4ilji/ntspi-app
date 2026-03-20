<?php

namespace App\Filament\Resources\AcademicJournalResource\Pages;

use App\Filament\Resources\AcademicJournalResource;
use App\Services\Filament\Traits\SeoGenerate;
use Filament\Resources\Pages\CreateRecord;

class CreateAcademicJournal extends CreateRecord
{
    use SeoGenerate;

    protected static string $resource = AcademicJournalResource::class;


    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['search_data'] = $this->generateSearchData($data['main_info']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $this->createSeo($this->record);
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
    private function getFirstBlockByName(string $name, array $content) : array|null
    {
        $data = null;
        foreach ($content as $block) {
            $data = ($block['type'] === $name) ? $block : null;
            break;
        }
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
