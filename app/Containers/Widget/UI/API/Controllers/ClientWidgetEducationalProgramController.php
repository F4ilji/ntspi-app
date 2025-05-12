<?php

namespace App\Containers\Widget\UI\API\Controllers;

use App\Containers\Education\Models\EducationalProgram;
use App\Containers\Search\UI\API\Transformers\EducationalProgramSearchResource;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use App\Ship\Enums\Education\EducationalProgramStatus;
use Illuminate\Support\Facades\Cache;

class ClientWidgetEducationalProgramController extends Controller
{
    public function index()
    {
        return Cache::remember(
            CacheKeys::EDUCATION_PROGRAMS_PREFIX->value . 'search_list',
            now()->addDay(), // Кешируем на 1 день
            function () {
                return EducationalProgramSearchResource::collection(
                    EducationalProgram::query()
                        ->where('status', EducationalProgramStatus::PUBLISHED)
                        ->orderBy('name', 'desc')
                        ->get()
                );
            }
        );
    }
}
