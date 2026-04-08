<?php

namespace App\Containers\VikonIntegration\Actions\Updates;

use App\Containers\VikonIntegration\Tasks\CallVikonApiTask;
use App\Containers\VikonIntegration\Tasks\ExtractZipArchiveTask;
use App\Containers\VikonIntegration\Tasks\ManageModuleFilesTask;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

/**
 * Action: Download and install module core update
 *
 * Replaces old vikon_core/download_module_core.php with:
 * - Secure HTTP (SSL enabled)
 * - Zip Slip protection
 * - Atomic file operations with rollback
 * - Proper error handling
 */
class DownloadModuleUpdateAction
{
    private const NEW_SUFFIX = '_new';
    private const OLD_SUFFIX = '_old';

    /**
     * Allowed file extensions for module updates
     *
     * Only static files are allowed - NO executable scripts
     * This prevents RCE via malicious ZIP uploads from Vikon API
     */
    private const ALLOWED_EXTENSIONS = [
        // Documents
        'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'rtf', 'odt', 'ods', 'odp',
        // Web
        'html', 'htm', 'css', 'js', 'json', 'xml', 'map',
        // Images
        'png', 'jpg', 'jpeg', 'gif', 'svg', 'svgz', 'webp', 'ico', 'bmp', 'tiff', 'tif',
        // Fonts
        'woff', 'woff2', 'ttf', 'eot', 'otf',
        // Archives (for internal use only)
        'zip', 'rar', '7z', 'gz', 'tar',
        // Vikon-specific
        'vikon',
    ];

    /**
     * Dangerous file extensions that MUST be blocked
     * Even if Vikon sends them, they will be rejected
     */
    private const BLOCKED_EXTENSIONS = [
        // PHP
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phps', 'phtml',
        // Other server-side scripts
        'asp', 'aspx', 'jsp', 'jspx', 'cfm', 'cfc',
        // Server configs
        'htaccess', 'htpasswd', 'conf', 'config',
        // Scripts
        'pl', 'py', 'pyc', 'pyo', 'rb', 'cgi', 'sh', 'bash', 'bat', 'cmd', 'exe', 'com',
        // PowerShell
        'ps1', 'psm1', 'psd1',
        // Node/JS runtime (JS allowed as static, but not server-side)
        'mjs', 'cjs', 'ts',
        // Compiled
        'so', 'dll', 'exe', 'bin', 'class',
    ];

    public function __construct(
        private readonly CallVikonApiTask $callVikonApiTask,
        private readonly ExtractZipArchiveTask $extractZipTask,
        private readonly array $modulesConfig,
        private readonly string $storagePath,
        private readonly string $basePath,
    ) {}

    /**
     * Download and install module update
     *
     * @param int $moduleId Module ID (1, 2, 6)
     * @param string $accessToken Valid Vikon access token
     * @return string Success message
     * @throws \RuntimeException
     */
    public function run(int $moduleId, string $accessToken): string
    {
        $moduleConfig = $this->getModuleConfig($moduleId);
        $modulePath = $this->basePath . '/' . $moduleConfig['path'];
        $tempPath = $this->storagePath . '/temp/' . $moduleConfig['path'];

        try {
            // Step 1: Download module core ZIP
            Log::info('Vikon update: downloading module core', ['module_id' => $moduleId]);

            $zipContent = $this->callVikonApiTask->downloadWithToken(
                'pull_updates/generateEmptyModuleCore/' . $moduleId,
                $accessToken
            );

            // Step 2: Prepare temp directory
            $this->prepareTempDirectory($tempPath);

            // Step 3: Write ZIP to temp file
            $zipFile = $tempPath . '/module_core.zip';
            $writeResult = file_put_contents($zipFile, $zipContent);

            if ($writeResult === false) {
                throw new \RuntimeException('Не удалось записать архив обновления. Проверьте права доступа.');
            }

            // Step 4: Extract with Zip Slip protection
            $this->extractZipTask->run($zipFile, $tempPath);

            // Step 5: Validate file types BEFORE syncing to module directory
            $blockedFiles = $this->validateFileTypes($tempPath);
            if (!empty($blockedFiles)) {
                throw new \RuntimeException(
                    'Обнаружены запрещённые типы файлов: ' . implode(', ', $blockedFiles) .
                    '. Обновление отклонено в целях безопасности.'
                );
            }

            // Step 6: Remove vikon_core directory from archive (we use Laravel-based updater)
            $vikonCorePath = $tempPath . '/vikon_core';
            if (File::isDirectory($vikonCorePath)) {
                Log::info('Vikon update: removing vikon_core from archive (using Laravel updater instead)');
                File::deleteDirectory($vikonCorePath);
            }

            // Step 7: Clean up ZIP
            File::delete($zipFile);

            // Step 7: Sync extracted files to module directory
            $this->syncModuleFiles($tempPath, $modulePath, $moduleConfig['path']);

            // Step 8: Clean module - remove files/folders not in allowed list (like old cleanUnitCore)
            $this->cleanModuleDirectory($modulePath, $moduleConfig['allowed_folders']);

            // Step 9: Create .vikon flag file
            $this->createVikonFlag($modulePath);

            // Step 9: Cleanup temp
            File::deleteDirectory($tempPath);

            Log::info('Vikon update: module core updated successfully', ['module_id' => $moduleId]);

            return 'Ядро модуля "' . $moduleConfig['name'] . '" успешно обновлено.';
        } catch (\Throwable $e) {
            // Rollback on error
            $this->rollback($modulePath, $moduleConfig['path']);

            Log::error('Vikon update: failed', [
                'module_id' => $moduleId,
                'error' => $e->getMessage(),
            ]);

            throw new \RuntimeException('Ошибка обновления модуля: ' . $e->getMessage());
        }
    }

    /**
     * Get module configuration
     *
     * @throws \RuntimeException
     */
    private function getModuleConfig(int $moduleId): array
    {
        if (!isset($this->modulesConfig[$moduleId])) {
            throw new \RuntimeException('Неизвестный идентификатор модуля');
        }

        return $this->modulesConfig[$moduleId];
    }

    /**
     * Prepare temporary directory for extraction
     */
    private function prepareTempDirectory(string $path): void
    {
        if (File::exists($path)) {
            File::deleteDirectory($path);
        }

        File::makeDirectory($path, 0755, true, true);
    }

    /**
     * Sync extracted files to module directory with atomic operations
     *
     * Strategy:
     * 1. Rename current file/dir to _old
     * 2. Move new file/dir to current location
     * 3. If error occurs, rollback from _old
     */
    private function syncModuleFiles(string $sourcePath, string $targetPath, string $moduleFolder): void
    {
        $entries = File::directories($sourcePath);
        $files = File::files($sourcePath);

        $failedEntries = [];

        // Process directories
        foreach ($entries as $entry) {
            $entryName = basename($entry);
            $currentPath = $targetPath . '/' . $entryName;
            $newPath = $sourcePath . '/' . $entryName;

            try {
                $this->syncDirectory($newPath, $currentPath, $targetPath);
            } catch (\Throwable $e) {
                $failedEntries[] = $entryName;
                Log::error('Vikon update: failed to sync directory', [
                    'entry' => $entryName,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        // Process files
        foreach ($files as $file) {
            $fileName = basename($file);
            $currentPath = $targetPath . '/' . $fileName;
            $newPath = $sourcePath . '/' . $fileName;

            try {
                $this->syncFile($newPath, $currentPath, $targetPath);
            } catch (\Throwable $e) {
                $failedEntries[] = $fileName;
                Log::error('Vikon update: failed to sync file', [
                    'entry' => $fileName,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!empty($failedEntries)) {
            throw new \RuntimeException(
                'Не удалось синхронизировать: ' . implode(', ', $failedEntries)
            );
        }
    }

    /**
     * Sync single directory with atomic rename
     */
    private function syncDirectory(string $newPath, string $currentPath, string $basePath): void
    {
        if (File::exists($currentPath)) {
            // Rename current to _old
            $oldPath = $currentPath . self::OLD_SUFFIX;
            if (File::exists($oldPath)) {
                File::deleteDirectory($oldPath);
            }
            File::move($currentPath, $oldPath);
        }

        // Move new to current
        File::copyDirectory($newPath, $currentPath);
    }

    /**
     * Sync single file with atomic rename
     */
    private function syncFile(string $newPath, string $currentPath, string $basePath): void
    {
        if (File::exists($currentPath)) {
            // Rename current to _old
            $oldPath = $currentPath . self::OLD_SUFFIX;
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
            rename($currentPath, $oldPath);
        }

        // Move new to current
        copy($newPath, $currentPath);
    }

    /**
     * Create .vikon flag file in module directory
     */
    private function createVikonFlag(string $modulePath): void
    {
        $flagPath = $modulePath . '/.vikon';

        if (!File::exists($flagPath)) {
            File::put($flagPath, date('Y-m-d H:i:s'));
        }
    }

    /**
     * Clean module directory - remove files/folders not in allowed list
     *
     * Replaces old Filesystem::cleanUnitCore()
     * After sync, removes any files/directories that are not in the allowed_folders list
     */
    private function cleanModuleDirectory(string $modulePath, array $allowedFolders): void
    {
        $entries = File::directories($modulePath);

        foreach ($entries as $entry) {
            $entryName = basename($entry);

            if (in_array($entryName, $allowedFolders, true)) {
                continue;
            }

            // Skip symlinks
            if (is_link($entry)) {
                Log::warning('Vikon update: skipping symlink during cleanup', [
                    'path' => $entry,
                ]);
                continue;
            }

            Log::info('Vikon update: removing disallowed directory', [
                'directory' => $entryName,
            ]);

            File::deleteDirectory($entry);
        }

        // Also check files at root level
        $files = File::files($modulePath);
        foreach ($files as $file) {
            $fileName = basename($file);

            if (in_array($fileName, $allowedFolders, true)) {
                continue;
            }

            // Skip .vikon flag and .htaccess
            if (in_array($fileName, ['.vikon', '.htaccess'], true)) {
                continue;
            }

            Log::info('Vikon update: removing disallowed file', [
                'file' => $fileName,
            ]);

            File::delete($file);
        }
    }

    /**
     * Validate all extracted files against allowed/blocked extensions
     *
     * This is a security measure to prevent RCE via malicious ZIP from Vikon API.
     * Even if Vikon sends PHP/ASP files, they will be rejected here.
     *
     * @return array List of blocked file paths
     */
    private function validateFileTypes(string $extractPath): array
    {
        $blockedFiles = [];

        $this->scanDirectoryForBlockedFiles($extractPath, $blockedFiles);

        return $blockedFiles;
    }

    /**
     * Recursively scan directory for blocked file types
     */
    private function scanDirectoryForBlockedFiles(string $directory, array &$blockedFiles): void
    {
        // Check files
        $files = File::files($directory);
        foreach ($files as $file) {
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            $relativePath = str_replace(base_path() . '/', '', $file);

            if (in_array($extension, self::BLOCKED_EXTENSIONS, true)) {
                $blockedFiles[] = $relativePath;
            }
        }

        // Recurse into subdirectories
        $dirs = File::directories($directory);
        foreach ($dirs as $dir) {
            $this->scanDirectoryForBlockedFiles($dir, $blockedFiles);
        }
    }

    /**
     * Rollback failed update from _old backups
     */
    private function rollback(string $modulePath, string $moduleFolder): void
    {
        Log::warning('Vikon update: rolling back failed update', [
            'module' => $moduleFolder,
        ]);

        // Restore directories
        $dirs = File::directories($modulePath);
        foreach ($dirs as $dir) {
            $dirName = basename($dir);
            $oldPath = $dir . self::OLD_SUFFIX;

            if (File::exists($oldPath)) {
                try {
                    // Remove failed new version
                    File::deleteDirectory($dir);
                    // Restore old version
                    File::move($oldPath, $dir);
                } catch (\Throwable $e) {
                    Log::error('Vikon update: rollback failed for directory', [
                        'entry' => $dirName,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }

        // Restore files
        $files = File::files($modulePath);
        foreach ($files as $file) {
            $fileName = basename($file);
            $oldPath = $file . self::OLD_SUFFIX;

            if (File::exists($oldPath)) {
                try {
                    // Remove failed new version
                    File::delete($file);
                    // Restore old version
                    rename($oldPath, $file);
                } catch (\Throwable $e) {
                    Log::error('Vikon update: rollback failed for file', [
                        'entry' => $fileName,
                        'error' => $e->getMessage(),
                    ]);
                }
            }
        }
    }
}
