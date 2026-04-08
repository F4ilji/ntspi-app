<?php

namespace App\Containers\VikonIntegration\Tasks;

use ZipArchive;

/**
 * Task: Safely extracts ZIP archives with Zip Slip protection
 *
 * Unlike old vikon_core which used unpackZip() without path validation,
 * this task validates every entry to prevent directory traversal attacks.
 */
class ExtractZipArchiveTask
{
    /**
     * Extract ZIP archive to destination with security checks
     *
     * @param string $zipPath Absolute path to ZIP file
     * @param string $destination Absolute path to extraction directory
     * @return bool
     * @throws \RuntimeException
     */
    public function run(string $zipPath, string $destination): bool
    {
        if (!file_exists($zipPath)) {
            throw new \RuntimeException("ZIP file not found: {$zipPath}");
        }

        if (!is_writable(dirname($destination))) {
            throw new \RuntimeException("Destination directory is not writable: " . dirname($destination));
        }

        $zip = new ZipArchive;
        $openResult = $zip->open($zipPath);

        if ($openResult !== true) {
            throw new \RuntimeException("Failed to open ZIP archive (code: {$openResult})");
        }

        // Zip Slip protection: validate every entry
        $this->validateZipEntries($zip, $destination);

        // Extract with overwrite
        $extractResult = $zip->extractTo($destination);

        $zip->close();

        if (!$extractResult) {
            throw new \RuntimeException('Failed to extract ZIP archive');
        }

        return true;
    }

    /**
     * Validate all ZIP entries to prevent Zip Slip attack
     *
     * @throws \RuntimeException
     */
    private function validateZipEntries(ZipArchive $zip, string $destination): void
    {
        $realDestination = realpath($destination);

        if ($realDestination === false) {
            throw new \RuntimeException('Destination directory does not exist');
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            // Check for path traversal patterns
            if ($this->containsPathTraversal($filename)) {
                throw new \RuntimeException(
                    "Potentially malicious ZIP entry detected: {$filename}"
                );
            }

            // Resolve full path and verify it's within destination
            $fullPath = realpath($realDestination . '/' . $filename);

            if ($fullPath === false) {
                // File doesn't exist yet (will be created), check parent directory
                $parentDir = dirname($realDestination . '/' . $filename);
                if (strpos($parentDir, $realDestination) !== 0) {
                    throw new \RuntimeException(
                        "ZIP entry escapes destination directory: {$filename}"
                    );
                }
            } elseif (strpos($fullPath, $realDestination) !== 0) {
                throw new \RuntimeException(
                    "ZIP entry escapes destination directory: {$filename}"
                );
            }
        }
    }

    /**
     * Check if filename contains path traversal patterns
     */
    private function containsPathTraversal(string $filename): bool
    {
        // Check for directory traversal sequences
        if (strpos($filename, '..') !== false) {
            return true;
        }

        // Check for absolute paths on Windows
        if (preg_match('/^[a-zA-Z]:/', $filename)) {
            return true;
        }

        // Check for absolute paths on Unix
        if (strpos($filename, '/') === 0) {
            return true;
        }

        return false;
    }
}
