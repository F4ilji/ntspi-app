<?php

namespace App\Http\Controllers;

use App\Enums\PostStatus;
use App\Models\Page;
use App\Models\Post;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemapController extends Controller
{
    public function index()
    {


    }

    protected function generatePages(Sitemap $sitemap)
    {
        $pages = Page::query()
            ->where('is_visible', true)
            ->where('code', 200)
            ->get();

        $this->addUrlsToSitemap($sitemap, $pages, function($page) {
            return [
                'path' => "/{$page->path}",
                'lastModificationDate' => $page->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function generatePosts(Sitemap $sitemap)
    {
        $posts = Post::query()
            ->where('status', PostStatus::PUBLISHED)
            ->get();

        $this->addUrlsToSitemap($sitemap, $posts, function($post) {
            return [
                'path' => "/news/{$post->slug}",
                'lastModificationDate' => $post->updated_at,
                'priority' => 0.5,
            ];
        });
    }

    protected function addUrlsToSitemap(Sitemap $sitemap, $items, callable $callback)
    {
        foreach ($items as $item) {
            $urlData = $callback($item);
            $sitemap->add(Url::create($urlData['path'])
                ->setLastModificationDate($urlData['lastModificationDate'])
                ->setPriority($urlData['priority']));
        }
    }
}
