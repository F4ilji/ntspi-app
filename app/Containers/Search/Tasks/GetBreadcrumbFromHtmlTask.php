<?php

namespace App\Containers\Search\Tasks;

use DiDom\Document;

class GetBreadcrumbFromHtmlTask
{
    public function run(string $html): ?string
    {
        $document = new Document($html);
        $breadcrumbs = $document->first('ol.breadcrumb')?->find('li') ?? [];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            if ($index === 2) { // Индексация с 0 → третий элемент = 2
                return trim($breadcrumb->text());
            }
        }

        return null;
    }
}
