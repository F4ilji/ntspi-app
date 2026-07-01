<?php

namespace App\Containers\Dashboard\Actions\EducationalPrograms;

use App\Containers\Education\Models\EducationalProgram;
use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Support\Str;

class CreateEducationalProgramAction
{
    public function run(array $data): EducationalProgram
    {
        $data['uuid'] = Str::uuid()->toString();
        $data['slug'] = $this->generateUniqueSlug($data['name'], $data['lvl_edu'] ?? null);
        $data['inner_code'] = $data['inner_code'] ?? '';
        $data['learning_forms'] = $data['learning_forms'] ?? [];

        if (empty($data['direction_study_id'])) {
            $data['direction_study_id'] = null;
        }

        return EducationalProgram::create($data);
    }

    private function generateUniqueSlug(string $name, mixed $lvlEdu): string
    {
        $slug = Str::slug($name);

        if ($lvlEdu) {
            $level = LevelEducational::tryFrom($lvlEdu);
            if ($level) {
                $slug = $slug . '-' . Str::lower($level->name);
            }
        }

        $originalSlug = $slug;
        $count = 1;

        while (EducationalProgram::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
