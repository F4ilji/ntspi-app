<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\ClientFullInfoPersonResource;
use App\Http\Resources\ClientNavigationResource;
use App\Http\Resources\MainSectionResource;
use App\Http\Resources\UserDetailResource;
use App\Http\Resources\UserResource;
use App\Models\MainSection;
use App\Models\User;
use App\Models\UserDetail;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PersonController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

    public function show(string $slug)
    {
        $cacheKey = CacheKeys::USER_PREFIX->value . md5($slug);

        [$person, $seo] = Cache::remember($cacheKey, now()->addHours(24), function () use ($slug) {
            $person = User::query()->with(['userDetail', 'departments_work.faculty', 'departments_teach.faculty', 'divisions', 'faculties', 'seo'])
                ->where('slug', $slug)
                ->firstOrFail();
            $seo = $this->seoPageProvider->getSeoForModel($person);
            return [
                new ClientFullInfoPersonResource($person),
                $seo
            ];
        });

        return Inertia::render('Client/Persons/Show', compact('person', 'seo'));
    }
}
