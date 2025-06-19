<?php

namespace App\Http\Controllers;

use App\Containers\AdditionalEducation\Models\AdditionalEducation;
use App\Containers\AdditionalEducation\Models\AdditionalEducationCategory;
use App\Containers\AppStructure\Models\Page;
use App\Containers\Article\Enums\PostStatus;
use App\Containers\Article\Models\Post;
use App\Containers\Education\Models\AdmissionCampaign;
use App\Containers\Event\Models\Event;
use App\Containers\Event\UI\WEB\Transformers\EventThumbnailResource;
use App\Containers\Widget\UI\API\Transformers\PostThumbnailResource;
use App\Ship\Enums\Education\LevelEducational;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class MainController extends Controller
{
    public function index()
    {
        // Кешируем данные на 60 минут (можно изменить время по необходимости)
//        $admissionCampaign = Cache::remember('admission_campaign', now()->addHour(), function () {
//            return $this->getAdmissionCampaign();
//        });

//        $educations = Cache::remember('educations_data', now()->addHour(), function () {
//            return $this->getEducationsData();
//        });

        $educations = $this->getEducationsData();

        $posts = Cache::remember('posts_recent', now()->addHour(), function () {
            return $this->getRecentPosts();
        });

//        $events = Cache::remember('upcoming_events', now()->addHour(), function () {
//            return $this->getUpcomingEvents();
//        });

        $events = $this->getUpcomingEvents();

        $path = route('index', null, false);
        $page = Cache::remember('page_' . $path, now()->addHour(), function () use ($path) {
            return Page::where('path', $path)->first();
        });

        $seo = $page->seo ?? null;

        return Inertia::render('Main', compact('posts', 'events', 'educations', 'seo'));
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
            Event::select('title', 'slug', 'event_date_start', 'event_time_start' , 'address', 'is_online', 'category_id')
                ->where('event_date_start', '>=', $event_date_start)
                ->orderBy('event_date_start', 'asc')
                ->limit(3)
                ->get()
        );
    }
}
