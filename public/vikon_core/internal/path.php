<?php

class Path
{
    public static $vikonCoreFolder = 'vikon_core';
    public static $functionalFolder = 'update';
    public static $srcExecutor = 'src_executor.php';
    public static $executorFile = 'executor.php';
    private static $foldersCore = array();

    private static $systemDots = array('.', '..');
    public static $n_pstfx = '_new';
    public static $o_pstfx = '_old';

    /**
     * Инициализация статических путей
     * @param array $foldersCore
     */
    public static function init($foldersCore)
    {
        self::$foldersCore = $foldersCore;
    }

    /**
     * @param $path - Путь относительно которого необходимо вернуть систмные точки (., ..)
     * @return array
     */
    public static function getSystemDots($path)
    {
        $systemDots = array();
        foreach (self::$systemDots as $systemDot) {
            $systemDots[] = $systemDot;
            $systemDots[] = $path.'/'.$systemDot;
        }
        return $systemDots;
    }

    /**
     * Возвращает абсолютный путь к главному ядру
     *
     * @return string
     */
    public static function getCoreRootPath()
    {
        return self::join(self::normalize(dirname(dirname(dirname(__FILE__)))), self::$vikonCoreFolder);
    }

    /**
     * Возращает путь к функциональной папке главного ядра.
     * Функциональная папка - папка в рамках которой проихсодит синхронизация и обновление частей и ядер
     * vikon_core/update
     *
     * @return string
     */
    public static function getFunctionalPath()
    {
        return self::join(self::getCoreRootPath(), self::$functionalFolder);
    }

    /**
     * Возвращает абсолютный путь к папке модуля по его идентификатору
     *
     * @param string $moduleId
     * @return string
     */
    public static function getModuleRootPath($moduleId)
    {
        $parentFolderCore = self::normalize(dirname(self::getCoreRootPath()));
        return self::join($parentFolderCore, self::$foldersCore[$moduleId]);
    }

    /**
     * Возвращает абсолютный путь к папке версий
     * @return string
     */
    public static function getTmpVersionPath()
    {
        return self::join(self::getCoreRootPath(), 'tmp', 'versions');
    }

    /**
     * @param int $moduleId Идентификатор модуля (1, 2 или 6).
     * @return string|null Возвращает путь к директории модуля или null, если модуль не найден.
     */
    public static function getFsPathByModule($moduleId)
    {
        return self::join(self::getModuleRootPath($moduleId), 'files');
    }

    /**
     * Возращает путь к исходному коду для синхронизации ядра
     *
     * @return string
     */
    public static function getSrcForExecutorPath()
    {
        return self::join(self::getFunctionalPath(), self::$srcExecutor);
    }

    /**
     * Возвращает, является ли путь базовым для другого пути.
     */
    public static function isBasePath($basePath, $ofPath)
    {
        $basePath = self::canonicalize($basePath);
        $ofPath = self::canonicalize($ofPath);

        $basePath = rtrim($basePath, '/').'/';
        $ofPath = $ofPath.'/';

        return 0 === strpos($ofPath, $basePath);
    }

    /**
     * Объединяет несколько частей пути в один канонический путь
     *
     * @return string
     */
    public static function join()
    {
        $paths = func_get_args();

        $finalPath = null;
        $wasScheme = false;

        foreach ($paths as $path) {
            if ('' === $path) {
                continue;
            }

            if (null === $finalPath) {
                $finalPath = $path;
                $wasScheme = (strpos($path, '://') !== false);
                continue;
            }

            $lastChar = substr($finalPath, -1);
            if ('/' !== $lastChar && '\\' !== $lastChar) {
                $finalPath .= '/';
            }

            if ($wasScheme) {
                $finalPath .= $path;
            } else {
                $finalPath .= ltrim($path, '/');
            }

            $wasScheme = false;
        }

        if ($finalPath === null) {
            return '';
        }

        return self::canonicalize($finalPath);
    }

    /**
     * Преобразует путь в канонический вид (убирает '.', '..', двойные слеши)
     *
     * @param string $path
     * @return string
     */
    private static function canonicalize($path)
    {
        $path = self::normalize($path);

        $parts = explode('/', $path);
        $absolutes = array();

        foreach ($parts as $part) {
            if ('' === $part || '.' === $part) {
                continue;
            }
            if ('..' === $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        $normalized = implode('/', $absolutes);

        if ('/' === substr($path, 0, 1)) {
            $normalized = '/' . $normalized;
        }

        return $normalized;
    }

    /**
     * Проверяет возможность дальнейшей работы с заданным путем, дальнейшие действия должны проходить в рамках
     * базовых путей vikon_core, или путей к модулю ($moduleId)
     *
     * @param int|null $moduleId - null - vikon_core, или id модуля
     */
    public static function isActionAllowedThisPath($path, $moduleId = null)
    {
        if ($moduleId === null && !Path::isBasePath(Path::getCoreRootPath(), $path)) {
            return false;
        }
        if ($moduleId && !Path::isBasePath(Path::getModuleRootPath($moduleId), $path)) {
            return false;
        }
        return true;
    }

    /**
     * Нормализует слеши в пути
     *
     * @param string $path
     * @return string
     */
    public static function normalize($path)
    {
        return str_replace('\\', '/', $path);
    }
}
