<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;
use App\Containers\InstituteStructure\Models\Division;
use Illuminate\Support\Str;

class UpdateDivisionAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(Division $division, array $data): Division
    {
        // Генерируем search_data из описания только если описание передано и не пусто
        if (isset($data['description']) && !empty($data['description'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['description']);
        }

        $division->update($data);

        // Обновляем SEO
        $this->updateSeo($division, $data);

        return $division;
    }

    private function updateSeo(Division $division, array $data): void
    {
        $title = $data['title'] ?? $division->title;
        $description = null;

        // Извлекаем description из первого paragraph блока
        if (!empty($data['description'])) {
            foreach ($data['description'] as $block) {
                if ($block['type'] === 'paragraph') {
                    $description = strip_tags($block['data']['content']);
                    break;
                }
            }
        }

        $seoData = [
            'title' => $title,
            'description' => Str::limit(htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'), 160),
        ];

        if ($division->seo) {
            $division->seo->update($seoData);
        } else {
            $division->seo()->create($seoData);
        }
    }
}
