<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Enums\EducationalProgramStatus;
use App\Http\Resources\EducationalProgramSearchResource;
use App\Models\EducationalProgram;
use Illuminate\Http\Request;
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
