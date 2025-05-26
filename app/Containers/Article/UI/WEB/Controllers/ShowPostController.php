<?php

namespace App\Containers\Article\UI\WEB\Controllers;

use App\Containers\Article\Models\Post;
use App\Containers\Article\UI\WEB\Transformers\PostItemResource;
use App\Ship\Enums\CacheKeys;
use App\Ship\Contracts\SeoServiceInterface;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;


class ShowPostController extends \App\Ship\Controllers\Controller
{
    public function __construct(private readonly SeoServiceInterface $seoPageProvider){}

    public function __invoke(Request $request, $slug): \Inertia\Response
    {
        $postData = Cache::remember(
            CacheKeys::POST_PREFIX->value . $slug,
            now()->addHours(1),
            fn() => Post::where('slug', $slug)
                ->where('publish_at', '<', Carbon::now())
                ->firstOrFail()
        );

        $seo = Cache::remember(
            CacheKeys::POST_PREFIX->value . 'seo_' . $slug,
            now()->addHours(1),
            fn() => $this->seoPageProvider->getSeoForModel($postData)
        );

        return inertia()->render('Client/Posts/Show', [
            'post' => new PostItemResource($postData),
            'seo' => $seo,
        ]);
    }
}