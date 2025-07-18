<?php

namespace App\Containers\AppStructure\Tasks;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class AddUrlsToSitemapTask
{
    public function run(Sitemap $sitemap, $items, callable $callback)
    {
        foreach ($items as $item) {
            $urlData = $callback($item);
            $url = route($urlData['route'], $urlData['params']);
            $sitemap->add(Url::create($url)
                ->setLastModificationDate($urlData['lastModificationDate'])
                ->setPriority($urlData['priority']));
        }
    }
}
