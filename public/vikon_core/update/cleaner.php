<?php
//todo new_core_after del

require_once dirname(__FILE__) . '/../internal/config.php';
require_once dirname(__FILE__) . '/../internal/helper.php';
require_once dirname(__FILE__) . '/../internal/filesystem.php';

if (!isset($_GET['is_access_remove_old_scripts_update'])) {
    $isAccessRemoveOldScriptsUpdate = 0;
}

if (!in_array((int) $_GET['is_access_remove_old_scripts_update'], array(0, 1))) {
    $isAccessRemoveOldScriptsUpdate = 0;
}

$isAccessRemoveOldScriptsUpdate = (int) $_GET['is_access_remove_old_scripts_update'];

Path::init($modulesByPathDeploy);

$vikonRootPath = Path::getCoreRootPath();

//remove executor
$executorPath = Path::join($vikonRootPath, Path::$executorFile);
if (!Filesystem::remove($executorPath, false)) {
    sendErrorResponse(500, 'Ошибка при очистке ядра. Не удалось удалить исполняемый файл:' . $executorPath);
}

//fail
//vikon_core-latest
$vikonCoreLatestPath = Path::join($vikonRootPath, 'vikon_core-latest');
if (!Filesystem::remove($vikonCoreLatestPath, true)) {
    sendErrorResponse(500, 'Ошибка при очистке ядра. Не удалось удалить исполняемый файл:' . $vikonCoreLatestPath);
}

//Удаляем старые скрипты в папке sveden/update
if ($isAccessRemoveOldScriptsUpdate) {
    $svedenRoot = Path::getModuleRootPath(SVEDEN);
    if (!file_exists($svedenRoot)) {
        sendSuccessResponse('Временные файлы успешно удалены.');
    }
    $svedenFuncPath = Path::join($svedenRoot, 'update');
    if (!file_exists($svedenFuncPath)) {
        sendSuccessResponse('Временные файлы успешно удалены.');
    }
    $entriesFunc = Filesystem::safeScandir($svedenFuncPath);
    if (!$entriesFunc) {
        sendSuccessResponse('Временные файлы успешно удалены.');
    }
    $excludeEntries = array('index.php');
    foreach ($entriesFunc as $entry) {
        if (in_array($entry, $excludeEntries)) {
            continue;
        }
        if (!Filesystem::remove(Path::join($svedenFuncPath, $entry), true, SVEDEN)) {
            sendErrorResponse(500, 'Ошибка при очистке ядра. Не удалось удалить исполняемый файл:' . Path::join($svedenFuncPath, $entry));
        }
    }
}
sendSuccessResponse('Временные файлы успешно удалены.');
