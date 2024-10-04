<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected array $seoData;


    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->seoData = $this->generateSeo($data);
        $data['preview_text'] = $this->setPreviewText($data);
        $data['publish_at'] = $this->setPublishDateTime($data['status'], $this->record->publish_at);
        $data['search_data'] = $this->generateSearchData($data['content']);
        $data['reading_time'] = $this->calculateReadingTime($data['search_data']);

        return $data;
    }

    protected function afterSave(): void
    {
        $this->record->seo()->update($this->seoData);
    }

    private function setPreviewText(array $data) : string
    {
        $rowData = $this->getBlockBySeoActiveState('paragraph', $data['content']);
        $preview_text = strip_tags($rowData['data']['content']);
        return Str::limit($preview_text, 160);
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



    private function generateSeo(array $data) : array
    {
        $title = $data['title'];
        $rowData = $this->getFirstBlockByName('paragraph', $data['content']);
        $description = strip_tags($rowData['data']['content']);
        $image = ($this->record->preview !== null) ? $this->record->preview : null;

        return [
            'title' => $title,
            'description' => Str::limit($description, 160),
            'image' => $image,
        ];
    }

    private function setPublishDateTime($status, $publish_at)
    {
        if ($publish_at !== null) {
            return $publish_at;
        }
        return PostStatus::tryFrom($status) === PostStatus::PUBLISHED ? Carbon::now() : null;
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

    private function calculateReadingTime(string $text): int
    {

        // Calculate the number of words in the text
        $wordCount = str_word_count($text,0,"АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя");




        // Calculate the average reading speed in words per minute
        $wordsPerMinute = 120; // You can adjust this value based on your desired reading speed

        // Calculate the reading time in minutes
        $readingTime = $wordCount / $wordsPerMinute;

        // Round the reading time to the nearest integer
        $readingTime = round($readingTime);


        return $readingTime;
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
}
