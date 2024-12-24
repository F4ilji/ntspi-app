<?php

namespace App\Http\Controllers;

use App\Enums\EducationalProgramStatus;
use App\Enums\LevelEducational;
use App\Enums\PostStatus;
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
use App\Models\DirectionStudy;
use App\Models\EducationalProgram;
use App\Models\Event;
use App\Models\MainSection;
use App\Models\MainSlider;
use App\Models\Page;
use App\Models\Post;
use App\Services\Filament\Icon\ArrayToCollectionService;
use App\Services\Vicon\EducationalProgram\EducationalProgramService;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index()
    {
        $admissionCampaign = $this->getAdmissionCampaign();
        $educations = $this->getEducationsData();
        $sliders = $this->getActiveSliders();
        $posts = $this->getRecentPosts();
        $events = $this->getUpcomingEvents();

        $path = route('index', null, false);
        $page = Page::where('path', $path)->first();
        $seo = $page->seo ?? null;

        return Inertia::render('Main', compact('posts', 'events', 'sliders', 'educations', 'seo'));
    }

    private function getAdmissionCampaign()
    {
        $info = AdmissionCampaign::first()->info ?? [];

        return collect($info)->reduce(function ($carry, $a) {
            $lvl = LevelEducational::from((int)$a['edu_name'])->name;
            $carry[$lvl] = [
                'total_programs' => $a['total_programs'],
                'places' => [
                    'och_count' => $a['och_count'],
                    'zaoch_count' => $a['zaoch_count'],
                    'budget_places' => $a['budget_places'],
                    'non_budget_places' => $a['non_budget_places']
                ],
            ];
            return $carry;
        }, []);
    }

    private function getEducationsData()
    {
        return [
            'admission_campaign' => $this->getAdmissionCampaign(),
            'additional_education' => [
                'educations_count' => AdditionalEducation::where('is_active', true)->count(),
                'categories_count' => AdditionalEducationCategory::where('is_active', true)->count(),
            ],
        ];
    }

    private function getActiveSliders()
    {
        return ClientMainSliderResource::collection(
            MainSlider::where('is_active', true)
                ->orderBy('sort', 'asc')
                ->get()
        );
    }

    private function getRecentPosts()
    {
        return PostThumbnailResource::collection(
            Post::select('title', 'slug', 'authors', 'preview_text', 'category_id', 'preview', 'search_data', 'publish_at', 'created_at')
                ->with('category')
                ->where('publish_at', '<', Carbon::now())
                ->where('status', '=', PostStatus::PUBLISHED)
                ->orderBy('publish_at', 'desc')
                ->limit(3)
                ->get()
        );
    }

    private function getUpcomingEvents()
    {
        $event_date_start = (new DateTime())->format('Y-m-d');

        return EventThumbnailResource::collection(
            Event::select('title', 'slug', 'event_date_start', 'address', 'is_online', 'category_id')
                ->where('event_date_start', '>=', $event_date_start)
                ->orderBy('event_date_start', 'asc')
                ->limit(3)
                ->get()
        );
    }
}
