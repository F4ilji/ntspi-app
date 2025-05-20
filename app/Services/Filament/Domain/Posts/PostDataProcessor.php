<?php

namespace App\Services\Filament\Domain\Posts;

use App\Containers\Article\Enums\PostStatus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PostDataProcessor
{
    /**
     * Обрабатывает данные перед созданием поста.
     *
     * @param array $data
     * @return array
     */
    public function processCreate(array $data): array
    {
        // Удаляем ненужные данные
        unset($data['publication']);

        // Устанавливаем текст для предпросмотра
        $data['preview_text'] = $this->setPreviewText($data);

        if (($data['status']->value === PostStatus::PUBLISHED->value)) {
            $data['publish_at'] = $this->setPublishDateTime();
        }

        // Устанавливаем время публикации
        if ($data['publish_setting']['publish_after'] === true) {
            $data['publish_at'] = $this->setPublishDateTimeInFuture($data['publish_setting']);
        }

        unset($data['publish_setting']);

        // Генерируем данные для поиска
        $data['search_data'] = $this->generateSearchData($data['content']);

        // Рассчитываем время чтения
        $data['reading_time'] = $this->calculateReadingTime($data['search_data']);

        // Устанавливаем ID текущего пользователя
        $data['user_id'] = auth()->id();


        return $data;
    }

    public function processUpdate(array $data): array
    {
        if (isset($data['status']) && !$data['status'] instanceof PostStatus) {
            $data['status'] = PostStatus::tryFrom($data['status']);
        }
        unset($data['publication']);

        // Устанавливаем текст для предпросмотра
        $data['preview_text'] = $this->setPreviewText($data);


        if (!$data['publish_at'] && ($data['status']->value === PostStatus::PUBLISHED->value)) {
            $data['publish_at'] = $this->setPublishDateTime();
        }

        // Устанавливаем время публикации
        if ((isset($data['publish_setting']['publish_after']) && $data['publish_setting']['publish_after']) ||
            (isset($data['publish_setting']['publish_at']) && $data['publish_setting']['publish_at'])) {
            $data['publish_at'] = $this->setPublishDateTimeInFuture($data['publish_setting']);
        }

        unset($data['publish_setting']);

        // Генерируем данные для поиска
        $data['search_data'] = $this->generateSearchData($data['content']);

        // Рассчитываем время чтения
        $data['reading_time'] = $this->calculateReadingTime($data['search_data']);

        return $data;
    }

    /**
     * Устанавливает текст для предпросмотра.
     *
     * @param array $data
     * @return string
     */
    private function setPreviewText(array $data): string
    {
        $rowData = $this->getBlockBySeoActiveState('paragraph', $data['content']);
        if ($rowData === null) {
            $rowData = $this->getFirstBlockByName('paragraph', $data['content']);
        }

        $previewText = $rowData ? html_entity_decode(strip_tags($rowData['data']['content'])) : '';
        return Str::limit($previewText, 160);
    }

    /**
     * Устанавливает время публикации.
     *
     * @param array $publishSetting
     * @param string $status
     * @return Carbon|null
     */
    private function setPublishDateTime(): ?Carbon
    {
        return Carbon::now();
    }

    private function setPublishDateTimeInFuture(array $publishSetting): ?Carbon
    {
        if (!empty($publishSetting['publish_at'])) {
            return Carbon::parse($publishSetting['publish_at']);
        }

        return null;
    }

    /**
     * Генерирует данные для поиска.
     *
     * @param array $content
     * @return string
     */
    private function generateSearchData(array $content): string
    {
        $result = "";
        foreach ($content as $block) {
            $result .= $this->getDataFromBlocks($block);
        }

        // Удаляем лишние пробелы и переносы строк
        $result = preg_replace('/\s+/', ' ', $result);
        $result = trim($result);

        return strtolower($result);
    }

    /**
     * Извлекает данные из блоков контента.
     *
     * @param array $block
     * @return string
     */
    private function getDataFromBlocks(array $block): string
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
                    }
                }
                break;
        }
        return $data;
    }

    /**
     * Рассчитывает время чтения.
     *
     * @param string $text
     * @return int
     */
    private function calculateReadingTime(string $text): int
    {
        $wordCount = str_word_count($text, 0, "АаБбВвГгДдЕеЁёЖжЗзИиЙйКкЛлМмНнОоПпРрСсТтУуФфХхЦцЧчШшЩщЪъЫыЬьЭэЮюЯя");
        $wordsPerMinute = 120; // Средняя скорость чтения
        return max(1, round($wordCount / $wordsPerMinute));
    }

    /**
     * Находит первый блок по имени.
     *
     * @param string $name
     * @param array $content
     * @return array|null
     */
    private function getFirstBlockByName(string $name, array $content): ?array
    {
        foreach ($content as $block) {
            if ($block['type'] === $name) {
                return $block;
            }
        }
        return null;
    }

    /**
     * Находит блок по SEO-активности.
     *
     * @param string $name
     * @param array $content
     * @return array|null
     */
    private function getBlockBySeoActiveState(string $name, array $content): ?array
    {
        foreach ($content as $block) {
            if ($block['type'] === $name && ($block['data']['seo_active'] ?? false)) {
                return $block;
            }
        }
        return null;
    }
}