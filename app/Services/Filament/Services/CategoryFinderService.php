<?php

namespace App\Services\Filament\Services;

use DiDom\Document;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class CategoryFinderService
{
    const FILES_DIR = 'sveden';

    public function getCategories()
    {
        $file = public_path(self::FILES_DIR . '/' . 'index.html');
        $html = file_get_contents($file);

        $document = new Document($html);
        $dropdownMenu = $document->first('ul.dropdown-menu');
        $links = $dropdownMenu->find('a');
        $categories = [];

        foreach ($links as $link) {
            $category = trim($link->text());
            $categories[] = $category;
        }

        return $categories;
    }


}