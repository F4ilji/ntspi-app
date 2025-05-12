<?php

namespace App\Containers\Search\Services;

use DiDom\Document;

class HtmlContentExtractorService
{
    public function getContent(string $html): ?array
    {
        $document = new Document($html);
        $content = $document->first('.vikon-content');
        $breadcrumb = $content->first('.row');
        $content->firstInDocument('.row')->remove();

        return [$content->html(), $breadcrumb->html()] ?? null;
    }


}