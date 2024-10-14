<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Http\Resources\AdditionalEducationSearchResource;
use App\Http\Resources\PostThumbnailResource;
use App\Models\AdditionalEducation;
use App\Models\Post;
use Illuminate\Http\Request;

class ClientWidgetAdditionalEducationalProgramController extends Controller
{
    public function index()
    {
        return AdditionalEducationSearchResource::collection(
            AdditionalEducation::query()
                ->where('is_active', true)
                ->orderBy('title', 'desc')
                ->get());
    }

}
