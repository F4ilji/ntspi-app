<?php

namespace App\Containers\Search\Tasks;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use App\Containers\Search\Tasks\ExtractHtmlContentTask;
use App\Containers\Search\Tasks\GetBreadcrumbFromHtmlTask;

class BuildStaticFileIndexTask
{
    const CACHE_KEY = 'static_html_index_v2';
    const FILES_DIR = 'sveden';
    const CACHE_TTL = 86400; // 24 часа

    public function run(): array
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function() {
            try {
                $index = [];
                $directory = public_path(self::FILES_DIR);

                if (!is_dir($directory)) {
                    Log::error("Directory not found: {$directory}");
                    return [];
                }

                $iterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::SELF_FIRST
                );

                foreach ($iterator as $file) {
                    if ($file->isFile() && $this->isHtmlFile($file)) {
                        $html = file_get_contents($file->getPathname());
                        [$content, $breadcrumb] = app(ExtractHtmlContentTask::class)->run($html);
                        if ($content !== false) {
                            $breadcrumb = app(GetBreadcrumbFromHtmlTask::class)->run($breadcrumb);
                            $text = $this->normalizeText(strip_tags($content));
                            $index[$file->getPathname()] = [
                                'title' => $this->getFirstH1Content($content),
                                'content' => $text,
                                'category' => $breadcrumb ?? null,
                            ];
                        }
                    }
                }

                return $index;
            } catch (\Exception $e) {
                Log::error('Index creation error: ' . $e->getMessage());
                return [];
            }
        });
    }

    protected function isHtmlFile(\SplFileInfo $file): bool
    {
        $extension = strtolower($file->getExtension());
        return in_array($extension, ['html', 'htm']);
    }

    protected function normalizeText(string $text): string
    {
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim($text);
        return Str::lower($text);
    }

    public function getFirstH1Content(string $content): ?string
    {
        try {
            if (preg_match('/<h1[^>]*>(.*?)<\/h1>/is', $content, $matches)) {
                return $this->normalizeText($matches[1]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Failed to get H1: ' . $e->getMessage());
            return null;
        }
    }
}
