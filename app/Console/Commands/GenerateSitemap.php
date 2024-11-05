<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Event;
use App\Models\Page;
use App\Models\Post;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class GenerateSitemap extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Генерирует карту сайта';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create();

        $this->generatePages($sitemap);
        $this->generatePosts($sitemap);
        $this->generateEvents($sitemap);

        $sitemap->writeToFile(public_path('sitemap.xml'));
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

    protected function generateEvents(Sitemap $sitemap)
    {
        $events = Event::query()
            ->get();

        $this->addUrlsToSitemap($sitemap, $events, function($event) {
            return [
                'path' => "/events/{$event->slug}",
                'lastModificationDate' => $event->updated_at,
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
