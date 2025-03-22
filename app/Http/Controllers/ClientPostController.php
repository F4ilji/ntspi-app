<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ClientBreadcrumbPage;
use App\Http\Resources\ClientBreadcrumbSection;
use App\Http\Resources\ClientBreadcrumbSubSection;
use App\Http\Resources\ClientNavigationResource;
use App\Http\Resources\ClientPostListResource;
use App\Http\Resources\ClientTagResource;
use App\Http\Resources\MainSectionResource;
use App\Http\Resources\PageResource;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\MainSection;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Services\App\Breadcrumb\BreadcrumbService;
use Carbon\Carbon;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ClientPostController extends Controller
{
    public function __construct(private readonly BreadcrumbService $breadcrumbService){}

    public function index(Request $request)
    {
        // Кешируем список тегов
        $tagIds = Cache::remember('tag_ids', now()->addHours(1), function () {
            return DB::table('taggables')
                ->distinct()
                ->select('tag_id')
                ->where('taggable_type', Post::class)
                ->get()
                ->pluck('tag_id');
        });

        // Кешируем теги
        $tags = Cache::remember('tags', now()->addHours(1), function () use ($tagIds) {
            return \Spatie\Tags\Tag::whereIn('id', $tagIds)->get();
        });

        // Кешируем посты с учетом фильтров
        $cacheKey = 'posts_' . md5(serialize($request->all()));
        $posts = Cache::remember($cacheKey, now()->addHours(1), function () use ($request) {
            return ClientPostListResource::collection(Post::query()
                ->with('category')
                ->select('title', 'slug', 'authors', 'category_id', 'preview', 'search_data', 'publish_at')
                ->where('status', '=', 'published')
                ->where('publish_at', '<', Carbon::now())
                ->when($request->input('search'), function ($query, $search) {
                    $query->whereRaw('LOWER(title) like ?', ["%".strtolower($search)."%"]);
                })
                ->when($request->input('category'), function ($query) use ($request) {
                    $slugs = $request->input('category');
                    if (is_array($slugs)) {
                        $query->whereHas('category', function ($query) use ($slugs) {
                            $query->whereIn('slug', $slugs);
                        });
                    }
                })
                ->when($request->input('tag'), function ($query, $slugs) {
                    if (is_array($slugs)) {
                        return $query->withAnyTags($slugs);
                    }

                    $slugsArray = explode(',', $slugs);
                    return $query->withAnyTags($slugsArray);
                })
                ->orderBy('publish_at', $request->input('sort', 'desc'))
                ->paginate(6)
                ->withQueryString());
        });

        // Кешируем категории
        $categories = Cache::remember('categories', now()->addHours(48), function () {
            return CategoryResource::collection(Category::has('posts')->get());
        });

        // Кешируем контент категорий
        $categoriesContent = [];
        if ($request->input('category')) {
            foreach ($request->input('category') as $item) {
                $cacheKey = 'category_content_' . $item;
                $categoriesContent[$item] = Cache::remember($cacheKey, now()->addHours(1), function () use ($item) {
                    return new CategoryResource(Category::where('slug', $item)->first());
                });
            }
        }

        // Кешируем контент тегов
        $tagsContent = [];
        if ($request->input('tag')) {
            foreach ($request->input('tag') as $item) {
                $cacheKey = 'tag_content_' . $item;
                $tagsContent[$item] = Cache::remember($cacheKey, now()->addHours(1), function () use ($item) {
                    return new ClientTagResource(DB::table('tags')
                        ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.ru'))"), $item)
                        ->first());
                });
            }
        }

        $filters = [
            'search_filter' => [
                'type' => 'search',
                'value' => $request->input('search'),
                'param' => 'search'
            ],
            'category_filter' => [
                'type' => 'category',
                'value' => $request->input('category'),
                'param' => 'category',
                'content' => $categoriesContent,
            ],
            'tag_filter' => [
                'type' => 'tag',
                'value' => $request->input('tag'),
                'param' => 'tag',
                'content' => $tagsContent
            ],
            'sortingBy_filter' => [
                'type' => 'sort',
                'value' => $request->input('sort'),
                'param' => 'sort',
            ],
        ];

        $breadcrumbs = $this->breadcrumbService->generateBreadcrumbs('client.post.index');



        return Inertia::render('Client/Posts/Index', compact('filters', 'posts', 'categories', 'tags', 'breadcrumbs'));
    }

    public function show(Request $request, $slug)
    {
        // Уникальный ключ для кеширования
        $cacheKey = 'post_' . md5($slug);

        // Пытаемся получить данные из кеша
        $data = Cache::remember($cacheKey, now()->addHours(1), function () use ($slug) {
            // Получаем пост
            $post = Post::where('slug', $slug)
                ->where('publish_at', '<', Carbon::now())
                ->firstOrFail();

            // Преобразуем пост в ресурс
            $postResource = new PostResource($post);

            $breadcrumbs = $this->breadcrumbService->generateBreadcrumbs('client.post.index');

            // SEO-данные
            $seo = $post->seo ?? null;

            // Возвращаем данные для кеширования
            return [
                'post' => $postResource,
                'breadcrumbs' => $breadcrumbs,
                'seo' => $seo,
            ];
        });

        // Возвращаем ответ с использованием кешированных данных
        return Inertia::render('Client/Posts/Show', $data);
    }


}
