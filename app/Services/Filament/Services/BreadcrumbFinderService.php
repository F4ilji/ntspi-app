<?php

namespace App\Services\Filament\Services;

use DiDom\Document;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class BreadcrumbFinderService
{
    const FILES_DIR = 'sveden';

    public function isSameCategory($html)
    {
        return $this->getBreadcrumb($html);
    }

    private function getBreadcrumb(string $html): ?string
    {
        $document = new Document($html);
        $breadcrumbs = $document->first('ol.breadcrumb')?->find('li') ?? [];

        foreach ($breadcrumbs as $index => $breadcrumb) {
            if ($index === 2) { // Индексация с 0 → третий элемент = 2
                return $breadcrumb->text();
            }
        }

        return null;
    }


}