<?php
//скрипт запускается оносительно vikon_core
$vikonDir = dirname(__FILE__);
require_once $vikonDir . '/internal/config.php';
require_once $vikonDir . '/internal/helper.php';
require_once $vikonDir . '/internal/filesystem.php';

if (!isset($_GET['access_token']) || !filterAccessToken($_GET['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}

$token = $_GET['access_token'];

$dlHandler = null;

try {
    $headers = array('Accept-Encoding: zip, gzip', 'Authorization: Bearer ' . $token);
    $zipCore = remoteRequest($apiDomen . 'pull_updates/generateEmptyCore', false, false, $headers);
    if ($zipCore->curlHasError) {
        throw new RuntimeException('CURL ' . $zipCore->curlErrorTxt);
    }

    if ($zipCore->code !== 200) {
        $zipCore->responseBody = json_decode($zipCore->responseBody);

        $err = 'Не удается скачать файл с обновлениями. ' . (!empty($zipCore->responseBody->message)
                ? $zipCore->responseBody->message
                : 'Неизвестная ошибка');
        sendErrorResponse($zipCore->code, $err);
    }

    $vikonFuncPath = Path::getCoreRootPath();
    $vikonZipCorePath = Path::join($vikonFuncPath, 'vikon_core-latest.zip');

    if (!Filesystem::removeZip($vikonZipCorePath, true)) {
        $err = 'Ошибка при распаковке ядра. Архив уже существует. Недостаточно прав для его удаления';
        sendErrorResponse(500, $err);
    }

    $dlHandler = fopen($vikonZipCorePath, 'w');
    if (!fwrite($dlHandler, $zipCore->responseBody)) {
        $err = 'Ошибка при распаковке ядра модуля. Не удалось записать архив. Проверьте права доступа и свободное место.';
        sendErrorResponse(500, $err);
    }

    if ($dlHandler) {
        fclose($dlHandler);
    }

    $vikonUnpackNewCorePath = Path::join($vikonFuncPath, 'vikon_core-latest');

    if (!Filesystem::remove($vikonUnpackNewCorePath, true)) {
        $err = 'Ошибка при распаковке ядра. Временная директория уже существует и не может быть удалена.';
        sendErrorResponse(500, $err);
    }

    $unpackResult = unpackZip($vikonZipCorePath, $vikonUnpackNewCorePath);
    if (!$unpackResult['success']) {
        sendErrorResponse(500, 'Ошибка при распаковке ядра. Проверьте права доступа и свободное место.');
    }
    Filesystem::removeZip($vikonZipCorePath, false);

    $vikonRootPath = Path::getCoreRootPath();

    $pathToFoldersNewCore = Path::join($vikonUnpackNewCorePath, Path::$vikonCoreFolder);
    $nameFoldersForSync = Filesystem::safeScandir($pathToFoldersNewCore);
    $isRestoreFail = false;
    $folderSyncFail = null;
    foreach ($nameFoldersForSync as $nameFolderSync) {
        $curFolder = Path::join($vikonRootPath, $nameFolderSync);
        $pathToNewFolder = Path::join($vikonUnpackNewCorePath, Path::$vikonCoreFolder, $nameFolderSync);

        if (file_exists($curFolder)) {
            $pathToNewFolderPostfix = Path::join($vikonRootPath, $nameFolderSync . Path::$n_pstfx);
            // try del vikon_core/assets_new
            if (!Filesystem::remove($pathToNewFolderPostfix, true)) {
                $folderSyncFail = $nameFolderSync;
                break;
            }
            // try replace vikon_core/vikon_core-latest/vikon_core/assets -> vikon_core/assets_new
            if (!Filesystem::replaceWithRename($pathToNewFolder, $pathToNewFolderPostfix)) {
                $folderSyncFail = $nameFolderSync;
                break;
            }

            $pathToOldFolderPostfix = Path::join($vikonRootPath, $nameFolderSync . Path::$o_pstfx);
            // try del vikon_core/assets_old
            if (!Filesystem::remove($pathToOldFolderPostfix, true)) {
                $folderSyncFail = $nameFolderSync;
                break;
            }
            // try replace vikon_core/assets -> vikon_core/assets_old
            if (!Filesystem::replaceWithRename($curFolder, $pathToOldFolderPostfix)) {
                $folderSyncFail = $nameFolderSync;
                break;
            }

            // vikon_core/assets_new -> vikon_core/assets
            if (!Filesystem::replaceWithRename($pathToNewFolderPostfix, $curFolder)) {
                $folderSyncFail = $nameFolderSync;
                break;
            }
        } else {
            // try replace vikon_core/vikon_core-latest/vikon_core/assets -> vikon_core/assets
            if (!Filesystem::replaceWithRename($pathToNewFolder, $curFolder)) {
                $folderSyncFail = $nameFolderSync;
                break;
            }
        }
    }
    //синхронизированные папки + временная папка с версиями
    $allFoldersNeedCore = array_merge($nameFoldersForSync, array('tmp'));
    if ($folderSyncFail !== null) {
        if (!Filesystem::restoreUnitCoreAfterFail($vikonRootPath, $allFoldersNeedCore)) {
            sendErrorResponse(500, 'Ошибка при синхронизации ядра. Не удалось восстановить ядро после ошибки.');
        } else {
            sendErrorResponse(500, 'Ошибка при синхронизации ядра. Не удалось синхронизировать папку: ' . $folderSyncFail);
        }
    }

    $successOrPath = Filesystem::cleanUnitCore($vikonRootPath, $allFoldersNeedCore);
    if (is_string($successOrPath)) {
        sendErrorResponse(500, 'Ошибка проверки целостности ядра. Не удалось удалить папку: ' . $successOrPath);
    }
    if (!$successOrPath) {
        sendErrorResponse(500, 'Ошибка проверки целостности ядра. Нет доступа к корневой папке ядра.');
    }
} catch (Exception $ex) {
    sendErrorResponse(500, $ex->getMessage());
}

sendSuccessResponse('Архив с базовыми обновлениями успешно загружен.');
