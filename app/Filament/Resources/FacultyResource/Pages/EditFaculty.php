<?php

namespace App\Filament\Resources\FacultyResource\Pages;

use App\Filament\Resources\FacultyResource;
use App\Services\Filament\Traits\SeoGenerate;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditFaculty extends EditRecord
{
    use SeoGenerate;

    protected static string $resource = FacultyResource::class;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['search_data'] = $this->generateSearchData($data['content']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->updateSeo($this->record);
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

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
