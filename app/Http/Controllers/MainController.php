<?php

namespace App\Http\Controllers;

use App\Http\Resources\AdditionalEducationResource;
use App\Http\Resources\ClientMainSliderResource;
use App\Http\Resources\ClientNavigationResource;
use App\Http\Resources\EventThumbnailResource;
use App\Http\Resources\MainSectionResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostThumbnailResource;
use App\Models\AdditionalEducation;
use App\Models\AdditionalEducationCategory;
use App\Models\AdmissionCampaign;
use App\Models\Event;
use App\Models\MainSection;
use App\Models\MainSlider;
use App\Models\Post;
use App\Services\Filament\Icon\ArrayToCollectionService;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class MainController extends Controller
{

    public function index()
    {
        $additional_educations = [
            'educations_count' => AdditionalEducation::where('is_active', true)->count(),
            'categories_count' => AdditionalEducationCategory::where('is_active', true)->count()
        ];
        $today = new DateTime();
        $event_date_start = $today->format('Y-m-d');
        $sliders = ClientMainSliderResource::collection(MainSlider::query()->where('is_active', true)->orderBy('sort', 'asc')->get());
        $posts = PostThumbnailResource::collection(Post::query()
            ->select('title', 'slug', 'authors', 'category_id', 'preview', 'search_data', 'created_at')
            ->with('category')
            ->where('status', '=', 'published')
            ->orderBy('id', 'desc')->limit(3)
            ->get());
        $events = EventThumbnailResource::collection(Event::query()
            ->select('title', 'slug', 'event_date_start', 'address', 'is_online', 'category_id')
            ->where('event_date_start', '>=', $event_date_start)
            ->orderBy('event_date_start', 'asc')->limit(3)->get());
        return Inertia::render('Main', compact('posts', 'events', 'sliders', 'additional_educations'));
    }
}
