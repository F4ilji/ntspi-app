<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class FilesystemTask
{
    public function isPathSafe(string $path, string $base): bool
    {
        $realBase = realpath($base);
        if ($realBase === false) return false;

        $realPath = realpath($path);
        if ($realPath !== false) {
            return str_starts_with($realPath, $realBase);
        }

        $parent = realpath(dirname($path));
        return $parent !== false && str_starts_with($parent, $realBase);
    }

    public function safeRemove(string $path, string $base, bool $recursive = false): bool
    {
        if (!$this->isPathSafe($path, $base)) {
            Log::warning('Path traversal blocked', ['path' => $path, 'base' => $base]);
            return false;
        }
        if (!file_exists($path)) return true;
        if (!is_dir($path)) return File::delete($path);
        return File::deleteDirectory($path);
    }

    public function safeMkdir(string $path): bool
    {
        if (File::isDirectory($path)) return true;
        return File::makeDirectory($path, 0755, true, true);
    }

    public function replaceDirectory(string $source, string $target, string $base): bool
    {
        if (!$this->isPathSafe($source, $base) || !$this->isPathSafe($target, $base)) {
            return false;
        }
        if (!File::exists($source)) return false;
        if (File::exists($target)) return false;

        $parent = dirname($target);
        if (!is_writable($parent)) return false;

        if (!File::makeDirectory($target, 0755, true, true)) return false;

        foreach (File::allFiles($source) as $file) {
            $relative = ltrim(str_replace($source, '', $file->getPathname()), '/');
            $dest = $target . '/' . $relative;
            $destDir = dirname($dest);
            if (!File::isDirectory($destDir)) {
                File::makeDirectory($destDir, 0755, true, true);
            }
            if (!copy($file->getPathname(), $dest)) return false;
        }

        return File::deleteDirectory($source);
    }

    public function validateFileTypes(string $directory): array
    {
        $allowed = [
            'html', 'htm',
            'css', 'js', 'map',
            'json', 'xml', 'txt',
            'png', 'jpg', 'jpeg', 'gif', 'svg', 'svgz', 'webp', 'ico', 'bmp',
            'ttf', 'woff', 'woff2', 'eot', 'otf',
            'pdf', 'doc', 'docx', 'xls', 'xlsx',
            'zip',
            'vikon',
        ];

        $found = [];

        foreach (File::allFiles($directory) as $file) {
            $ext = strtolower($file->getExtension());
            if (!in_array($ext, $allowed, true)) {
                $found[] = str_replace(base_path() . '/', '', $file->getPathname());
            }
        }

        return $found;
    }
}
