<?php

namespace App\Containers\Dashboard\Actions\Faculties;

use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;
use App\Containers\InstituteStructure\Models\Faculty;
use Illuminate\Support\Str;

class CreateFacultyAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(array $data): Faculty
    {
        // Генерируем slug если не передан
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Генерируем search_data из контента
        if (!empty($data['content'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['content']);
        }

        $faculty = Faculty::create($data);

        // Создаем SEO
        $this->generateSeo($faculty, $data);

        return $faculty;
    }

    private function generateSeo(Faculty $faculty, array $data): void
    {
        $title = $data['title'] ?? '';
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

        $faculty->seo()->create([
            'title' => $title,
            'description' => Str::limit(htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'), 160),
        ]);
    }
}
