<?php

namespace App\Containers\Search\Tasks;

use DiDom\Document;
use Illuminate\Support\Facades\Log;

class GetStaticFileCategoriesTask
{
    const FILES_DIR = 'sveden';

    public function run(): array
    {
        try {
            $file = public_path(self::FILES_DIR . '/' . 'index.html');
            if (!file_exists($file)) {
                Log::error("Category index file not found: {$file}");
                return [];
            }

            $html = file_get_contents($file);

            $document = new Document($html);
            $dropdownMenu = $document->first('ul.dropdown-menu');

            if ($dropdownMenu === null) {
                Log::warning("Dropdown menu not found in category index file: {$file}");
                return [];
            }

            $links = $dropdownMenu->find('a');
            $categories = [];

            foreach ($links as $link) {
                $category = trim($link->text());
                $categories[] = $category;
            }

            return $categories;
        } catch (\Exception $e) {
            Log::error('Error extracting categories: ' . $e->getMessage());
            return [];
        }
    }
}
