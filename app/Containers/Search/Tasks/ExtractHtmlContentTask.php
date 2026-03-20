<?php

namespace App\Containers\Search\Tasks;

use DiDom\Document;

class ExtractHtmlContentTask
{
    public function run(string $html): ?array
    {
        $document = new Document($html);
        $content = $document->first('.vikon-content');
        if ($content === null) {
            return null;
        }

        $breadcrumb = $content->first('.row');
        if ($breadcrumb !== null) {
            $content->firstInDocument('.row')->remove();
        }

        return [$content->html(), $breadcrumb->html() ?? null];
    }
}
