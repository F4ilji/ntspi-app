<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {

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


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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
