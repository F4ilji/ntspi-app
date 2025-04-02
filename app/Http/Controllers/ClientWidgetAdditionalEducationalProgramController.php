<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Enums\PostStatus;
use App\Http\Resources\AdditionalEducationSearchResource;
use App\Http\Resources\PostThumbnailResource;
use App\Models\AdditionalEducation;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ClientWidgetAdditionalEducationalProgramController extends Controller
{
    public function index()
    {
        return Cache::remember(
            CacheKeys::ADDITIONAL_EDUCATIONAL_PROGRAMS_PREFIX->value . 'search_list',
            now()->addDay(), // Кешируем на 1 день
            function () {
                return AdditionalEducationSearchResource::collection(
                    AdditionalEducation::query()
                        ->where('is_active', true)
                        ->orderBy('title', 'desc')
                        ->get()
                );
            }
        );
    }

}
