<?php

namespace App\Services\Filament\Domain\Posts;

use Illuminate\Support\Str;

class PostSeoGenerator
{
    /**
     * Генерирует SEO-данные для поста.
     *
     * @param array $data
     * @return array
     */
    public function generate(array $data): array
    {
        return [
            'title' => $this->extractSeoTitle($data),
            'description' => $this->extractSeoDescription($data['content']),
            'image' => $this->extractSeoImage($data),
        ];
    }

    /**
     * Извлекает SEO-заголовок.
     *
     * @param array $data
     * @return string
     */
    private function extractSeoTitle(array $data): string
    {
        return $data['title'];
    }

    /**
     * Извлекает SEO-описание.
     *
     * @param array $content
     * @return string
     */
    private function extractSeoDescription(array $content): string
    {
        $rowData = $this->getBlockBySeoActiveState('paragraph', $content);
        if ($rowData === null) {
            $rowData = $this->getFirstBlockByName('paragraph', $content);
        }

        $description = $rowData ? html_entity_decode(strip_tags($rowData['data']['content'])) : '';
        return Str::limit(htmlspecialchars($description, ENT_QUOTES, 'UTF-8'), 160);
    }

    /**
     * Извлекает SEO-изображение.
     *
     * @param array $data
     * @return string|null
     */
    private function extractSeoImage(array $data): ?string
    {
        return $data['preview'] ?? null;
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

    public function setPreviewText(array $data): ?string
    {
        $rowData = $this->getBlockBySeoActiveState('paragraph', $data['content']);
        if ($rowData === null) {
            $rowData = $this->getFirstBlockByName('paragraph', $data['content']);
        }

        if ($rowData !== null) {
            $previewText = html_entity_decode(strip_tags($rowData['data']['content']));
            return Str::limit($previewText, 160);
        }

        return null;
    }
}