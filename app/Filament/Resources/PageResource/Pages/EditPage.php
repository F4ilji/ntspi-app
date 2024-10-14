<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected array $seoData;



    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->seoData = $this->generateSeo($data);

        $data['search_data'] = $this->generateSearchData($data['content']);

        return $data;
    }

    protected function afterSave(): void
    {
        if ($this->record->is_registered == false) {
            if ($this->record->section == null) {
                $this->record->update(['path' => $this->record->slug]);
            } elseif ($this->record->section->mainSection == null) {
                $this->record->update(['path' => $this->record->path = $this->record->section->slug . '/' . $this->record->slug]);
            } else {
                $this->record->update(['path' => $this->record->path = $this->record->section->mainSection->slug . '/' . $this->record->section->slug . '/' . $this->record->slug]);
            }
        }

        $this->record->seo()->create($this->seoData);

    }



    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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
            'description' => Str::limit($description, 160),
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
    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }

}
