<?php

namespace App\Containers\Dashboard\Actions\Divisions;

use App\Containers\Dashboard\Tasks\Content\GenerateSearchDataTask;
use App\Containers\InstituteStructure\Models\Division;
use Illuminate\Support\Str;

class CreateDivisionAction
{
    public function __construct(
        private readonly GenerateSearchDataTask $generateSearchDataTask,
    ) {}

    public function run(array $data): Division
    {
        // Генерируем slug если не передан
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        // Генерируем search_data из описания
        if (!empty($data['description'])) {
            $data['search_data'] = $this->generateSearchDataTask->run($data['description']);
        }

        $division = Division::create($data);

        // Создаем SEO
        $this->generateSeo($division, $data);

        return $division;
    }

    private function generateSeo(Division $division, array $data): void
    {
        $title = $data['title'] ?? '';
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

        $division->seo()->create([
            'title' => $title,
            'description' => Str::limit(htmlspecialchars($description ?? '', ENT_QUOTES, 'UTF-8'), 160),
        ]);
    }
}
