<?php

namespace App\Containers\User\UI\WEB\Controllers;

use App\Containers\User\Models\User;
use App\Containers\User\UI\WEB\Transformers\FullInfoPersonResource;
use App\Ship\Contracts\SeoServiceInterface;
use App\Ship\Controllers\Controller;
use App\Ship\Enums\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class PersonController extends Controller
{
    public function __construct(readonly SeoServiceInterface $seoPageProvider){}

    public function show(string $slug)
    {
        $personData = Cache::remember(
            CacheKeys::USER_PREFIX->value . $slug,
            now()->addHours(24),
            fn() => User::with(['userDetail', 'departments_work.faculty', 'departments_teach.faculty', 'divisions', 'faculties'])
                ->where('slug', $slug)
                ->whereHas('userDetail')
                ->firstOrFail()
        );

        $seo = Cache::remember(
            CacheKeys::USER_PREFIX->value . 'seo_' . $slug,
            now()->addHours(24),
            fn() => $this->seoPageProvider->getSeoForModel($personData)
        );

        $person = new FullInfoPersonResource($personData);



        return Inertia::render('Client/Persons/Show', compact('person', 'seo'));
    }
}
