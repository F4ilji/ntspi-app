<?php

namespace App\Containers\Education\Jobs;

use App\Containers\Education\Models\DirectionStudy;
use App\Services\Vicon\DirectionStudy\DirectionStudyService;
use App\Ship\Enums\Education\LevelEducational;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateDirectionStudy implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $directionStudyService;


    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        $this->directionStudyService = new DirectionStudyService();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $naprs = [];
        $naprsUuid = $this->directionStudyService->getAllNaprsUuid();
        foreach ($naprsUuid as $uuid) {
            array_push($naprs, $this->directionStudyService->getNapr($uuid));
        }
        foreach ($naprs as $napr) {
            $slug = $this->generateUniqueSlug($napr->name_napr, DirectionStudy::class, LevelEducational::from($napr->lvl_edu));

            DirectionStudy::updateOrCreate(
                ['uuid' => $napr->uuid],
                [
                    'name' => $napr->name_napr,
                    'slug' => $slug,
                    'code' => $napr->kod_napr,
                    'lvl_edu' => $napr->lvl_edu,
                ]
            );
        };
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
