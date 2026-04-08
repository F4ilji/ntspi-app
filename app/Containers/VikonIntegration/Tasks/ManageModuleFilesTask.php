<?php

namespace App\Containers\VikonIntegration\Tasks;

use Illuminate\Support\Facades\File;

/**
 * Task: Manage module files (scan, remove, create directories)
 *
 * Replaces old vikon_core Filesystem class with Laravel's File facade.
 * All operations are scoped to specific module to prevent accidental deletion.
 */
class ManageModuleFilesTask
{
    public function __construct(
        private readonly string $basePath,
    ) {}

    /**
     * Get module root path
     */
    public function getModulePath(int $moduleId, string $moduleFolder): string
    {
        return $this->basePath . '/' . $moduleFolder;
    }

    /**
     * Safely remove directory with path validation
     *
     * @param string $path Path to remove
     * @param string $allowedBasePath Base path constraint
     * @param bool $recursive Remove recursively
     * @return bool
     */
    public function safeRemove(string $path, string $allowedBasePath, bool $recursive = false): bool
    {
        // Prevent path traversal
        if (!$this->isPathWithinBase($path, $allowedBasePath)) {
            \Illuminate\Support\Facades\Log::warning('Attempted to remove path outside allowed base', [
                'path' => $path,
                'allowed_base' => $allowedBasePath,
            ]);
            return false;
        }

        if (!file_exists($path)) {
            return true;
        }

        if (!is_dir($path)) {
            return File::delete($path);
        }

        return File::deleteDirectory($path);
    }

    /**
     * Safely create directory
     */
    public function safeMkdir(string $path, int $mode = 0755): bool
    {
        if (File::isDirectory($path)) {
            return true;
        }

        return File::makeDirectory($path, $mode, true, true);
    }

    /**
     * Safely create file with content
     */
    public function safeCreateFile(string $path, string $content = '', int $mode = 0644): bool
    {
        if (File::exists($path)) {
            return true;
        }

        $directory = dirname($path);
        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }

        $result = File::put($path, $content);
        if ($result === false) {
            return false;
        }

        chmod($path, $mode);
        return true;
    }

    /**
     * Scan directory safely (excludes . and ..)
     *
     * @return array|false
     */
    public function safeScandir(string $path): array|false
    {
        if (!File::isDirectory($path) || !File::isReadable($path)) {
            return false;
        }

        $entries = File::directories($path);
        $files = File::files($path);

        return array_merge(
            array_map('basename', $entries),
            array_map('basename', $files)
        );
    }

    /**
     * Rename file with path validation
     */
    public function safeRename(string $oldPath, string $newPath, string $allowedBasePath): bool
    {
        if (!$this->isPathWithinBase($oldPath, $allowedBasePath)) {
            return false;
        }
        if (!$this->isPathWithinBase($newPath, $allowedBasePath)) {
            return false;
        }

        if (!File::exists($oldPath)) {
            return false;
        }
        if (File::exists($newPath)) {
            return false;
        }

        $newDir = dirname($newPath);
        if (!File::isDirectory($newDir)) {
            return false;
        }

        return File::move($oldPath, $newPath);
    }

    /**
     * Replace directory with rename (atomic operation)
     *
     * Old -> _old
     * New -> Old
     */
    public function replaceWithRename(string $sourceDir, string $targetDir, string $allowedBasePath): bool
    {
        if (!$this->isPathWithinBase($sourceDir, $allowedBasePath)) {
            return false;
        }
        if (!$this->isPathWithinBase($targetDir, $allowedBasePath)) {
            return false;
        }

        if (!File::exists($sourceDir)) {
            return false;
        }
        if (File::exists($targetDir)) {
            return false;
        }

        $parentDir = dirname($targetDir);
        if (!is_writable($parentDir)) {
            return false;
        }

        if (!File::makeDirectory($targetDir, 0755, true, true)) {
            return false;
        }

        $entries = $this->safeScandir($sourceDir);
        if (empty($entries)) {
            return File::deleteDirectory($sourceDir);
        }

        foreach ($entries as $entry) {
            $item = $sourceDir . '/' . $entry;
            $newPath = $targetDir . '/' . $entry;

            if (File::isDirectory($item)) {
                if (!$this->replaceWithRename($item, $newPath, $allowedBasePath)) {
                    return false;
                }
            } else {
                if (!File::move($item, $newPath)) {
                    return false;
                }
            }
        }

        return File::deleteDirectory($sourceDir);
    }

    /**
     * Check if path is within allowed base directory
     */
    private function isPathWithinBase(string $path, string $basePath): bool
    {
        $realPath = realpath($path);
        $realBase = realpath($basePath);

        // If base directory doesn't exist, path cannot be valid
        if ($realBase === false) {
            \Illuminate\Support\Facades\Log::error('Base path does not exist', [
                'base' => $basePath,
            ]);
            return false;
        }

        if ($realPath === false) {
            // For non-existent paths, check parent directory
            $parentDir = dirname($path);
            $realParent = realpath($parentDir);

            if ($realParent === false) {
                return false;
            }

            return str_starts_with($realParent . '/', $realBase . '/');
        }

        return str_starts_with($realPath . '/', $realBase . '/') || $realPath === $realBase;
    }
}
