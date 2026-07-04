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

    public function atomicSwap(
        string $newEntryPath,
        string $currentEntryPath,
        string $baseDir,
        int $moduleId
    ): bool {
        if (!$this->isPathSafe($currentEntryPath, $baseDir)) {
            return false;
        }

        if (!File::exists($newEntryPath)) {
            return false;
        }

        $isFile = is_file($newEntryPath);
        $newPostfix = $currentEntryPath . '_new';
        $oldPostfix = $currentEntryPath . '_old';

        if (File::exists($currentEntryPath)) {
            // Step 1: Remove stale _new if exists
            if (File::exists($newPostfix)) {
                $isFile ? File::delete($newPostfix) : File::deleteDirectory($newPostfix);
            }

            // Step 2: Move new → _new
            if (!$this->moveEntry($newEntryPath, $newPostfix, $isFile)) {
                return false;
            }

            // Step 3: Remove stale _old if exists
            if (File::exists($oldPostfix)) {
                $isFile ? File::delete($oldPostfix) : File::deleteDirectory($oldPostfix);
            }

            // Step 4: Move current → _old
            if (!$this->moveEntry($currentEntryPath, $oldPostfix, $isFile)) {
                return false;
            }

            // Step 5: Move _new → current
            return $this->moveEntry($newPostfix, $currentEntryPath, $isFile);
        }

        // Entry doesn't exist — move directly
        return $this->moveEntry($newEntryPath, $currentEntryPath, $isFile);
    }

    public function restoreAfterFail(string $modulePath, array $allowedEntries, int $moduleId): bool
    {
        $entries = File::directories($modulePath);
        $entries = array_merge($entries, File::files($modulePath));

        foreach ($entries as $entryPath) {
            $name = basename($entryPath);

            if (str_ends_with($name, '_new')) {
                $baseName = substr($name, 0, -4);
                if (in_array($baseName, $allowedEntries, true)) {
                    if (is_dir($entryPath)) {
                        File::deleteDirectory($entryPath);
                    } else {
                        File::delete($entryPath);
                    }
                }
            }

            if (str_ends_with($name, '_old')) {
                $baseName = substr($name, 0, -4);
                if (in_array($baseName, $allowedEntries, true)) {
                    $originalPath = $modulePath . '/' . $baseName;
                    if (File::exists($originalPath)) {
                        if (is_dir($originalPath)) {
                            File::deleteDirectory($originalPath);
                        } else {
                            File::delete($originalPath);
                        }
                    }
                    rename($entryPath, $originalPath);
                }
            }
        }

        return true;
    }

    private function moveEntry(string $source, string $dest, bool $isFile): bool
    {
        if ($isFile) {
            $parent = dirname($dest);
            if (!File::isDirectory($parent)) {
                File::makeDirectory($parent, 0755, true, true);
            }
        }
        return rename($source, $dest);
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
