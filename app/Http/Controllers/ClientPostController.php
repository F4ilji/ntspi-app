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
use Carbon\Carbon;
use Doctrine\DBAL\Schema\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ClientPostController extends Controller
{
    public function index(Request $request)
    {
        $tagIds = DB::table('taggables')
            ->distinct()
            ->select('tag_id')
            ->where('taggable_type', Post::class)
            ->get()
            ->pluck('tag_id');

        $tags = \Spatie\Tags\Tag::whereIn('id', $tagIds)->get();
        $posts = ClientPostListResource::collection(Post::query()
            ->with('category')
            ->select('title', 'slug', 'authors', 'category_id', 'preview', 'search_data', 'publish_at')
            ->where('status', '=', 'published')
            ->where('publish_at', '<', Carbon::now())
            ->when(request()->input('search'), function ($query, $search) {
                $query->whereRaw('LOWER(title) like ?', ["%".strtolower($search)."%"]);
            })
            ->when(request()->input('category'), function ($query) {
                $slugs = request()->input('category');
                if (is_array($slugs)) {
                    $query->whereHas('category', function ($query) use ($slugs) {
                        $query->whereIn('slug', $slugs);
                    });
                }
            })
            ->when(request()->input('tag'), function ($query, $slugs) {
                if (is_array($slugs)) {
                    return $query->withAnyTags($slugs);
                }

                $slugsArray = explode(',', $slugs);
                return $query->withAnyTags($slugsArray);
            })
            ->orderBy('publish_at', request()->input('sort', 'desc'))

            ->paginate(6)
            ->withQueryString());

        $categories = CategoryResource::collection(Category::has('posts')->get());

        $categoriesContent = [];
        if (request()->input('category')) {
            foreach (request()->input('category') as $item) {
                $categoriesContent[$item] = new CategoryResource(Category::where('slug', $item)->first());
            }
        }

        $tagsContent = [];
        if (request()->input('tag')) {
            foreach (request()->input('tag') as $item) {
                $tagsContent[$item] = new ClientTagResource(DB::table('tags')
                    ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT(slug, '$.ru'))"), $item)
                    ->first());
            }
        }

        $filters = [
            'search_filter' => [
                'type' => 'search',
                'value' => request()->input('search'),
                'param' => 'search'
            ],
            'category_filter' => [
                'type' => 'category',
                'value' => request()->input('category'),
                'param' => 'category',
                'content' => $categoriesContent,
            ],
            'tag_filter' => [
                'type' => 'tag',
                'value' => request()->input('tag'),
                'param' => 'tag',
                'content' => $tagsContent
            ],
            'sortingBy_filter' => [
                'type' => 'sort',
                'value' => request()->input('sort'),
                'param' => 'sort',
            ],
        ];

        $routeUrl = route('client.post.index');
        $path = ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');

        $page = Page::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }


        return Inertia::render('Client/Posts/Index', compact('filters', 'posts', 'categories', 'tags', 'breadcrumbs'));
    }

    public function show(Request $request, $slug)
    {
        $post = new PostResource(Post::where('slug', $slug)->where('publish_at', '<', Carbon::now())->firstOrFail());
        $routeUrl = route('client.post.index');
        $path = ltrim(parse_url($routeUrl, PHP_URL_PATH), '/');

        $page = Page::where('path', '=', $path)->with('section.pages.section', 'section.mainSection')->first();

        if (isset($page->section)) {
            $breadcrumbs = [
                'mainSection' => new ClientBreadcrumbSection($page->section->mainSection),
                'subSection' => new ClientBreadcrumbSubSection($page->section),
                'page' => new ClientBreadcrumbPage($page),
            ];
        } else {
            $breadcrumbs = null;
        }

        $seo = $post->seo ?? null;


        return Inertia::render('Client/Posts/Show', compact('post', 'breadcrumbs', 'seo'));
    }



}
