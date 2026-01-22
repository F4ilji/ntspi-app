<?php
$updateDir = dirname(__FILE__);

require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';
require_once $updateDir . '/../internal/filesystem.php';

if (!isset($_POST['access_token']) || !filterAccessToken($_POST['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}
$token = $_POST['access_token'];

$headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
$res = remoteRequest($apiDomen . 'pull_updates/checkAccessJson', true, false, $headers);
if ($res->curlHasError) {
    sendErrorResponse(500, 'CURL ' . $res->curlErrorTxt);
}
if ($res->code !== 200) {
    $err = 'Ошибка проверки подлинности токена. '
        . (!empty($zipCore->responseBody->message) ? $zipCore->responseBody->message : 'Неизвестная ошибка');
    sendErrorResponse($res->code, $err);
}

$isAccessRemoveOldScriptsUpdate = 0;
if (isset($_POST['is_access_remove_old_scripts_update']) && in_array((int) $_POST['is_access_remove_old_scripts_update'], array(0, 1))) {
    $isAccessRemoveOldScriptsUpdate = (int) $_POST['is_access_remove_old_scripts_update'];
}

Path::init($modulesByPathDeploy);
$vikonRootPath = Path::getCoreRootPath();

//remove executor
$executorPath = Path::join($vikonRootPath, Path::$executorFile);
if (!Filesystem::remove($executorPath, false)) {
    sendErrorResponse(500, 'Ошибка при очистке ядра. Не удалось удалить исполняемый файл:' . $executorPath);
}

//fail
$vikonCoreLatestPath = Path::join($vikonRootPath, 'vikon_core-latest');
if (!Filesystem::remove($vikonCoreLatestPath, true)) {
    sendErrorResponse(500, 'Ошибка при очистке ядра. Не удалось удалить исполняемый файл:' . $vikonCoreLatestPath);
}

//todo new_core_after
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
