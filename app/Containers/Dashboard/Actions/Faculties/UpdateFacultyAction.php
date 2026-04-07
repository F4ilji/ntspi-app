<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;
use App\Containers\InstituteStructure\Models\Faculty;
use Illuminate\Support\Str;

class UpdateFacultyAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(Faculty $faculty, array $data): Faculty
    {
        // Генерируем search_data из контента только если контент передан и не пуст
        if (isset($data['content']) && !empty($data['content'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['content']);
        }

        $faculty->update($data);

        // Обновляем SEO
        $this->updateSeo($faculty, $data);

        return $faculty;
    }

    private function updateSeo(Faculty $faculty, array $data): void
    {
        $title = $data['title'] ?? $faculty->title;
        $description = null;

        // Извлекаем description из первого paragraph блока
        if (!empty($data['content'])) {
            foreach ($data['content'] as $block) {
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

        if ($faculty->seo) {
            $faculty->seo->update($seoData);
        } else {
            $faculty->seo()->create($seoData);
        }
    }
}
