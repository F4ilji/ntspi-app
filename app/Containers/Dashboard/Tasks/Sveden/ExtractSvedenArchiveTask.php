<?php

namespace App\Containers\Dashboard\Tasks\Sveden;

use ZipArchive;

class ExtractSvedenArchiveTask
{
    private const ALLOWED_FOLDERS = ['sveden', 'abitur'];

    private const PUBLIC_PATH = 'public';

    private const DANGEROUS_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml',
        'asp', 'aspx', 'jsp', 'cgi', 'pl',
        'sh', 'bash', 'zsh', 'exe', 'bat', 'cmd',
        'env', 'conf', 'ini', 'htaccess',
    ];

    public function run(string $zipPath): array
    {
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP file not found: {$zipPath}");
        }

        $zip = new ZipArchive();
        $openResult = $zip->open($zipPath);

        if ($openResult !== true) {
            throw new \RuntimeException("Failed to open ZIP archive (code: {$openResult})");
        }

        $this->validateZipEntries($zip);

        $rootFolders = $this->getRootFolders($zip);
        $allowedFolders = array_intersect($rootFolders, self::ALLOWED_FOLDERS);

        if (empty($allowedFolders)) {
            $zip->close();
            throw new \RuntimeException(
                'Archive does not contain sveden or abitur folders. Found: ' . implode(', ', $rootFolders ?: ['none'])
            );
        }

        $updated = [];

        foreach ($allowedFolders as $folder) {
            $this->extractFolder($zip, $folder);
            $updated[] = $folder;
        }

        $zip->close();

        return [
            'success' => true,
            'updated' => $updated,
            'skipped' => array_diff($rootFolders, self::ALLOWED_FOLDERS),
        ];
    }

    private function getRootFolders(ZipArchive $zip): array
    {
        $folders = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);
            $parts = explode('/', $name);

            if (count($parts) > 1 && !empty($parts[0])) {
                $folders[$parts[0]] = true;
            }
        }

        return array_keys($folders);
    }

    private function extractFolder(ZipArchive $zip, string $folderName): void
    {
        $destination = public_path($folderName);

        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $name = $zip->getNameIndex($i);

            if (strpos($name, $folderName . '/') !== 0) {
                continue;
            }

            $relativePath = substr($name, strlen($folderName . '/'));

            if (empty($relativePath)) {
                continue;
            }

            $targetPath = $destination . '/' . $relativePath;

            $this->validateFileExtension($relativePath);

            if (substr($name, -1) === '/') {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                $parentDir = dirname($targetPath);
                if (!is_dir($parentDir)) {
                    mkdir($parentDir, 0755, true);
                }

                $content = $zip->getFromIndex($i);
                file_put_contents($targetPath, $content);
            }
        }
    }

    private function validateFileExtension(string $filePath): void
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if (in_array($extension, self::DANGEROUS_EXTENSIONS, true)) {
            throw new \RuntimeException(
                "Potentially dangerous file detected: {$filePath}"
            );
        }
    }

    private function validateZipEntries(ZipArchive $zip): void
    {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            if (strpos($filename, '..') !== false) {
                throw new \RuntimeException(
                    "Potentially malicious ZIP entry detected: {$filename}"
                );
            }

            if (preg_match('/^[a-zA-Z]:/', $filename)) {
                throw new \RuntimeException(
                    "Potentially malicious ZIP entry detected: {$filename}"
                );
            }

            if (strpos($filename, '/') === 0) {
                throw new \RuntimeException(
                    "Potentially malicious ZIP entry detected: {$filename}"
                );
            }
        }
    }
}
