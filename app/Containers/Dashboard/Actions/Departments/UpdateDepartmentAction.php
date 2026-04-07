<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;
use App\Containers\InstituteStructure\Models\Department;
use Illuminate\Support\Str;

class UpdateDepartmentAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(Department $department, array $data): Department
    {
        // Генерируем search_data из контента только если контент передан и не пуст
        if (isset($data['content']) && !empty($data['content'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['content']);
        }

        $department->update($data);

        // Обновляем SEO
        $this->updateSeo($department, $data);

        return $department;
    }

    private function updateSeo(Department $department, array $data): void
    {
        $title = $data['title'] ?? $department->title;
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

        if ($department->seo) {
            $department->seo->update($seoData);
        } else {
            $department->seo()->create($seoData);
        }
    }
}
