<?php

require_once 'path.php';

class Filesystem
{
    public static function getExtension($path)
    {
        if ($path === '') {
            return '';
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);
        return strtolower($extension);
    }

    /**
     * Выполняет безопасный scandir, убирает системные точки
     *
     * @param $path
     * @return array|false - возращает entries внутри сканируемой папки или false, в случае отсутсвия прав (или других причин)
     */
    public static function safeScandir($path)
    {
        if (!is_dir($path) || !is_readable($path)) {
            return false;
        }
        $entries = scandir($path);
        if ($entries === false) {
            return false;
        }
        return array_diff($entries, Path::getSystemDots($path));
    }

    public static function removeZip($path, $withCheckRights)
    {
        if (!Path::isActionAllowedThisPath($path)) {
            return false;
        }
        if (empty($path) || !file_exists($path)) {
            return true;
        }

        if ($withCheckRights) {
            $checks = array(
                'is_file' => is_file($path),
                'is_zip' => self::getExtension($path) === 'zip',
                'is_writable' => is_writable($path)
            );

            foreach ($checks as $check) {
                if (!$check) {
                    return false;
                }
            }
        }

        return unlink($path);
    }

    /**
     * @param string $path
     * @param bool $withRecursive
     * @param int|null $moduleId - Важный параметр который дает понимание в рамках какой папки мы работаем
     * Если null - работаем в рамках vikon_core, переданный moduleId - даст путь к модулю
     * @return bool
     */
    public static function remove($path, $withRecursive, $moduleId = null)
    {
        if (!Path::isActionAllowedThisPath($path, $moduleId)) {
            return false;
        }
        if (!file_exists($path)) {
            return true;
        }

        if (!is_dir($path)) {
            return unlink($path);
        }

        if (!$withRecursive) {
            return rmdir($path);
        }

        $success = true;
        $entries = self::safeScandir($path);
        foreach ($entries as $entry) {
            $fullPath = Path::join($path, $entry);
            if (is_dir($fullPath) && !is_link($fullPath)) {
                if (!self::remove($fullPath, $withRecursive, $moduleId)) {
                    $success = false;
                }
            } else {
                if (!unlink($fullPath)) {
                    $success = false;
                }
            }
        }

        if ($success) {
            if (!rmdir($path)) {
                $success = false;
            }
        }

        return $success;
    }

    /**
     * Переносит и переименовывает папку и все содержимое рекурсивно
     * Перед использованием выполни Filesysyem::remove($targetDir)
     *
     * В случае с replaceWithRename перемещать мы можем в рамках vikon_core и в рамках модуля
     */
    public static function replaceWithRename($sourceDir, $targetDir, $moduleId = null)
    {
        if ($moduleId === null) {
            if (!Path::isBasePath(Path::getCoreRootPath(), $sourceDir)) {
                return false;
            }
            if (!Path::isBasePath(Path::getCoreRootPath(), $targetDir)) {
                return false;
            }
        } else {
            if (
                !Path::isBasePath(Path::getModuleRootPath($moduleId), $sourceDir)
                && !Path::isBasePath(Path::getCoreRootPath(), $sourceDir)
            ) {
                return false;
            }
            if (
                !Path::isBasePath(Path::getModuleRootPath($moduleId), $targetDir)
                && !Path::isBasePath(Path::getCoreRootPath(), $targetDir)
            ) {
                return false;
            }
        }
        if (!file_exists($sourceDir)) {
            return false;
        }

        if (file_exists($targetDir)) {
            return false;
        }

        $parentDir = dirname($targetDir);
        if (!is_writable($parentDir)) {
            return false;
        }

        if (!mkdir($targetDir, 0755, true)) {
            return false;
        }

        $entries = self::safeScandir($sourceDir);

        if (empty($entries)) {
            return rmdir($sourceDir);
        }

        foreach ($entries as $entry) {
            $item = Path::join($sourceDir, $entry);
            $newPath = Path::join($targetDir, $entry);

            if (is_dir($item)) {
                if (!self::replaceWithRename($item, $newPath, $moduleId)) {
                    return false;
                }
            } else {
                if (!rename($item, $newPath)) {
                    return false;
                }
            }
        }

        return rmdir($sourceDir);
    }

    public static function safeRenameFile($oldPath, $newPath, $moduleId = null)
    {
        if ($moduleId === null) {
            if (!Path::isBasePath(Path::getCoreRootPath(), $oldPath)) {
                return false;
            }
            if (!Path::isBasePath(Path::getCoreRootPath(), $newPath)) {
                return false;
            }
        } else {
            if (
                !Path::isBasePath(Path::getModuleRootPath($moduleId), $oldPath)
                && !Path::isBasePath(Path::getCoreRootPath(), $oldPath)
            ) {
                return false;
            }
            if (
                !Path::isBasePath(Path::getModuleRootPath($moduleId), $newPath)
                && !Path::isBasePath(Path::getCoreRootPath(), $newPath)
            ) {
                return false;
            }
        }
        if (!file_exists($oldPath)) {
            return false;
        }
        if (file_exists($newPath)) {
            return false;
        }
        $newDir = dirname($newPath);
        if (!file_exists($newDir)) {
            return false;
        }
        if (!is_writable($newDir)) {
            return false;
        }
        if (!is_writable($oldPath)) {
            return false;
        }
        $result = rename($oldPath, $newPath);
        if (!$result) {
            return false;
        }
        return true;
    }

    /**
     * @param string $restorePath - путь к ядру модуля которое мы будем восстанавливать
     * @param array $allowedRestoreFolders - разрешенные к восстановлению папки,
     *      т.е папки которые прилетели из архива, будем работать только с ними
     * @return bool
     */
    public static function restoreUnitCoreAfterFail($restorePath, $allowedRestoreFolders, $moduleId = null)
    {
        if (!Path::isActionAllowedThisPath($restorePath, $moduleId)) {
            return false;
        }
        if (!file_exists($restorePath)) {
            return false;
        }
        $restoreEntries = self::safeScandir($restorePath);
        if ($restoreEntries === false) {
            return false;
        }
        foreach ($restoreEntries as $restoreEntry) {
            $fullPathRestore = Path::join($restorePath, $restoreEntry);
            $baseName = substr($restoreEntry, 0, -4);
            if (
                file_exists($fullPathRestore)
                && substr($restoreEntry, -4) === Path::$n_pstfx
                && in_array($baseName, $allowedRestoreFolders)
            ) {
                self::remove($fullPathRestore, true, $moduleId);
            }

            if (
                file_exists($fullPathRestore)
                && substr($restoreEntry, -4) === Path::$o_pstfx
                && in_array($baseName, $allowedRestoreFolders)
            ) {
                $newPath = Path::join($restorePath, $baseName);

                if (file_exists($newPath)) {
                    self::remove($newPath, true, $moduleId);
                }

                self::replaceWithRename($fullPathRestore, $newPath, $moduleId);
            }
        }
        return true;
    }

    /**
     * @param $pathClean - Путь к ядру модуля или главному ядру, чтобы почистить его
     * @param $excludedEntries - папки и файлы которые не нужно чистить в ядре
     * @param int|null $moduleId - модуль id или false - когда проверяем ядро
     * @return bool|string
     */
    public static function cleanUnitCore($pathClean, $excludedEntries, $moduleId = null)
    {
        if (!Path::isActionAllowedThisPath($pathClean, $moduleId)) {
            return false;
        }

        $entries = self::safeScandir($pathClean);

        foreach ($entries as $entry) {
            $item = Path::join($pathClean, $entry);

            if (in_array($entry, $excludedEntries)) {
                continue;
            }

            if (is_dir($item) && !is_link($item)) {
                if (!self::remove($item, true, $moduleId)) {
                    return $item;
                }
            } else {
                if (!unlink($item)) {
                    return $item;
                }
            }
        }

        return true;
    }

    public static function safeMkdir($path, $mode, $moduleId = null)
    {
        if (!Path::isActionAllowedThisPath($path, $moduleId)) {
            return false;
        }
        if (file_exists($path)) {
            return true;
        }
        $parentDir = dirname($path);
        if (!is_dir($parentDir)) {
            return false;
        }
        if (!is_writable($parentDir)) {
            return false;
        }
        return mkdir($path, $mode);
    }

    public static function safeMkfile($filename, $mode, $content = '')
    {
        if (file_exists($filename)) {
            return true;
        }
        $directory = dirname($filename);
        if (!is_writable($directory)) {
            return false;
        }
        if (file_put_contents($filename, $content) === false) {
            return false;
        }
        if (!chmod($filename, $mode)) {
            return false;
        }
        return true;
    }

    public static function ensureValidDirectoryAndFileName($directory, $filename)
    {
        if ($directory !== null && (!is_string($directory) || !preg_match("/^[a-z]{3,4}$/", $directory))) {
            return false;
        }

        if (!is_string($filename) || strpos($filename, '/') !== false || strpos($filename, "\\") !== false) {
            return false;
        }
        return true;
    }
}
