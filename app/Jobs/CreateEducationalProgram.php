<?php

namespace App\Jobs;

use App\Containers\Education\Models\DirectionStudy;
use App\Containers\Education\Models\EducationalProgram;
use App\Services\Vicon\EducationalProgram\EducationalProgramService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CreateEducationalProgram implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * Create a new job instance.
     */

    private EducationalProgramService $educatinalProgramService;
    public function __construct()
    {
        $this->educatinalProgramService = app(EducationalProgramService::class);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $programs = [];
        $programsUuid = $this->educatinalProgramService->getAllProgramsUuid();

        foreach ($programsUuid as $uuid) {
            $program = $this->educatinalProgramService->getProgram($uuid);
            if ($program !== null) {
                array_push($programs, $program);
            } else {
                Log::error("Не удалось получить данные о программе с UUID: $uuid");
            }
        }

        foreach ($programs as $program) {
            $direction = DirectionStudy::where('uuid', $program->napr_uuid)->first();
            if ($direction !== null) {
                $slug = $this->generateUniqueSlug($program->name_op, EducationalProgram::class, LevelEducational::from($program->lvl_edu));
                EducationalProgram::updateOrCreate(
                    ['uuid' => $program->uuid],
                    [
                        'name' => $program->name_op,
                        'slug' => $slug, // Добавляем slug
                        'inner_code' => $program->inner_code,
                        'lvl_edu' => $program->lvl_edu,
                        'status' => $program->status,
                        'learning_forms' => $program->learning_forms,
                        'direction_study_id' => $direction->id,
                    ]
                );
            } else {
                Log::error("Не удалось найти направление обучения с UUID: {$program->napr_uuid}");
            }
        }
    }

    private function generateUniqueSlug($name, $model, $levelEducational) {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $slug = $originalSlug . '-' . Str::lower($levelEducational->name);
        $count = 1;

        while ($model::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }
}