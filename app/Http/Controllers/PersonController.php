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

        $personData = Cache::remember(
            CacheKeys::USER_PREFIX->value . $slug,
            now()->addHours(24),
            fn() => User::with(['userDetail', 'departments_work.faculty', 'departments_teach.faculty', 'divisions', 'faculties'])
                ->where('slug', $slug)
                ->firstOrFail()
        );

        $seo = Cache::remember(
            CacheKeys::USER_PREFIX->value . 'seo_' . $slug,
            now()->addHours(24),
            fn() => $this->seoPageProvider->getSeoForModel($personData)
        );

        $person = new ClientFullInfoPersonResource($personData);



        return Inertia::render('Client/Persons/Show', compact('person', 'seo'));
    }
}
