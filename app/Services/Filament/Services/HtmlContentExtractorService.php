<?php

namespace App\Services\Filament\Services;

use DiDom\Document;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

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