<?php

namespace App\Containers\VikonIntegration\Actions;

use App\Containers\VikonIntegration\Tasks\HttpTask;
use App\Containers\VikonIntegration\Tasks\FilesystemTask;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class UpdateCoreAction
{
    private const NEW_SUFFIX = '_new';
    private const OLD_SUFFIX = '_old';

    public function __construct(
        private readonly HttpTask $http,
        private readonly FilesystemTask $fs,
        private readonly array $modulesConfig,
        private readonly string $storagePath,
        private readonly string $basePath,
    ) {}

    public function run(int $moduleId, string $accessToken): string
    {
        $config = $this->modulesConfig[$moduleId] ?? throw new \RuntimeException('Неизвестный модуль');
        $modulePath = $this->basePath . '/' . $config['path'];
        $tempPath = $this->storagePath . '/temp/' . $config['path'];

        try {
            Log::info('Vikon: downloading module core', ['module' => $moduleId]);

            if ($moduleId === 2) {
                return $this->initAbiturModule($modulePath, $accessToken);
            }

            $zipContent = $this->http->downloadWithToken(
                'pull_updates/generateEmptyModuleCore/' . $moduleId,
                $accessToken
            );

            if (File::exists($tempPath)) File::deleteDirectory($tempPath);
            File::makeDirectory($tempPath, 0755, true, true);

            $zipFile = $tempPath . '/module.zip';
            file_put_contents($zipFile, $zipContent);

            $this->extractZip($zipFile, $tempPath);

            $blocked = $this->fs->validateFileTypes($tempPath);
            if (!empty($blocked)) {
                throw new \RuntimeException(
                    'Запрещённые файлы: ' . implode(', ', $blocked) . '. Обновление отклонено.'
                );
            }

            $vikonCorePath = $tempPath . '/vikon_core';
            if (File::isDirectory($vikonCorePath)) {
                File::deleteDirectory($vikonCorePath);
            }

            File::delete($zipFile);

            $this->syncFiles($tempPath, $modulePath);
            $this->cleanModule($modulePath, $config['allowed_folders']);
            File::put($modulePath . '/.vikon', date('Y-m-d H:i:s'));
            File::deleteDirectory($tempPath);

            Log::info('Vikon: module updated', ['module' => $config['name']]);
            return 'Модуль "' . $config['name'] . '" обновлён.';

        } catch (\Throwable $e) {
            Log::error('Vikon update failed', ['module' => $moduleId, 'error' => $e->getMessage()]);
            $this->rollback($modulePath);
            throw new \RuntimeException('Ошибка обновления: ' . $e->getMessage());
        }
    }

    private function extractZip(string $zipPath, string $destination): void
    {
        $zip = new ZipArchive;
        if ($zip->open($zipPath) !== true) {
            throw new \RuntimeException('Не удалось открыть ZIP');
        }

        $realDest = realpath($destination);
        $blocked = ['php', 'phtml', 'php5', 'php7', 'php8', 'phar'];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (str_contains($name, '..')) {
                $zip->close();
                throw new \RuntimeException("Zip Slip: {$name}");
            }

            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($ext, $blocked, true)) {
                $zip->close();
                throw new \RuntimeException("Blocked executable: {$name}");
            }

            $full = realpath($realDest . '/' . $name);
            if ($full !== false && !str_starts_with($full, $realDest)) {
                $zip->close();
                throw new \RuntimeException("Path escape: {$name}");
            }
        }

        $zip->extractTo($destination);
        $zip->close();
    }

    private function syncFiles(string $source, string $target): void
    {
        foreach (File::files($source) as $file) {
            $name = $file->getFilename();
            $targetPath = $target . '/' . $name;

            if (File::exists($targetPath)) {
                $oldPath = $targetPath . self::OLD_SUFFIX;
                @unlink($oldPath);
                rename($targetPath, $oldPath);
            }
            rename($file->getPathname(), $targetPath);
        }

        foreach (File::directories($source) as $dir) {
            $name = basename($dir);
            $targetPath = $target . '/' . $name;

            if (File::exists($targetPath)) {
                $oldPath = $targetPath . self::OLD_SUFFIX;
                $this->fs->safeRemove($oldPath, $target, true);
                rename($targetPath, $oldPath);
            }

            rename($dir, $targetPath);
        }
    }

    private function cleanModule(string $modulePath, array $allowed): void
    {
        foreach (File::directories($modulePath) as $dir) {
            $name = basename($dir);
            if (!in_array($name, $allowed, true) && !is_link($dir)) {
                File::deleteDirectory($dir);
            }
        }
        foreach (File::files($modulePath) as $file) {
            $name = $file->getFilename();
            if (!in_array($name, $allowed, true) && !in_array($name, ['.vikon', '.htaccess'], true)) {
                File::delete($file);
            }
        }
    }

    private function rollback(string $modulePath): void
    {
        foreach (File::directories($modulePath) as $dir) {
            $old = $dir . self::OLD_SUFFIX;
            if (File::exists($old)) {
                File::deleteDirectory($dir);
                File::move($old, $dir);
            }
        }
        foreach (File::files($modulePath) as $file) {
            $old = $file->getPathname() . self::OLD_SUFFIX;
            if (File::exists($old)) {
                File::delete($file);
                rename($old, $file->getPathname());
            }
        }
    }

    private function initAbiturModule(string $modulePath, string $accessToken): string
    {
        $response = $this->http->getWithToken(
            'pull_updates/generateEmptyModuleCore/2',
            $accessToken
        );
        $body = $response->json();

        if (!isset($body['success']) || $body['success'] !== true) {
            throw new \RuntimeException('ABITUR init failed: ' . ($body['message'] ?? 'Unknown error'));
        }

        if (!File::isDirectory($modulePath)) {
            File::makeDirectory($modulePath, 0755, true, true);
        }
        if (!File::isDirectory($modulePath . '/files')) {
            File::makeDirectory($modulePath . '/files', 0755, true, true);
        }
        File::put($modulePath . '/.vikon', date('Y-m-d H:i:s'));

        Log::info('Vikon: ABITUR module initialized');
        return 'Модуль "Абитуриент" инициализирован.';
    }
}
