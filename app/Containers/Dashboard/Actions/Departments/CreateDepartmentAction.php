<?php

namespace App\Containers\Dashboard\Actions\Departments;

use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;
use App\Containers\InstituteStructure\Models\Department;
use Illuminate\Support\Str;

class CreateDepartmentAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(array $data): Department
    {
        // Генерируем slug если не передан
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Генерируем search_data из контента
        if (!empty($data['content'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['content']);
        }

        $department = Department::create($data);

        // Создаем SEO
        $this->generateSeo($department, $data);

        return $department;
    }

    private function generateSeo(Department $department, array $data): void
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

        $department->seo()->create([
            'title' => $title,
            'description' => Str::limit(htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'), 160),
        ]);
    }
}
