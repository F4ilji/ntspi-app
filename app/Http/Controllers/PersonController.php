<?php

namespace App\Http\Controllers;

use App\Enums\CacheKeys;
use App\Http\Resources\ClientFullInfoPersonResource;
use App\Models\User;
use App\Services\App\Seo\SeoPageProvider;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PersonController extends Controller
{
    public function __construct(readonly SeoPageProvider $seoPageProvider){}

    public function show(string $slug)
    {
        $cacheKey = CacheKeys::USER_PREFIX->value . md5($slug);

        [$person, $seo] = Cache::remember($cacheKey, now()->addHours(24), function () use ($slug) {
            $person = User::query()->with(['userDetail', 'departments_work.faculty', 'departments_teach.faculty', 'divisions', 'faculties'])
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
