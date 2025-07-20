<?php

namespace App\Containers\InstituteStructure\Tasks;

use App\Containers\InstituteStructure\Models\Faculty;
use App\Containers\InstituteStructure\UI\WEB\Transformers\FacultyPreviewResource;
use App\Ship\Enums\CacheKeys;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class GetActiveFacultiesTask
{
    public function run(): AnonymousResourceCollection
    {
        return Cache::remember(
            CacheKeys::FACULTIES_PREFIX->value . 'active_list',
            now()->addDay(), // Кешируем на 1 день
            function () {
                return FacultyPreviewResource::collection(
                    Faculty::query()
                        ->where('is_active', true)
                        ->get()
                );
            }
        );
    }
}
