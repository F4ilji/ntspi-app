<?php

$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';
require_once $updateDir . '/../internal/filesystem.php';

if (!isset($_GET['access_token']) || !filterAccessToken($_GET['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}

if (!isset($_GET['module_id']) || !filterInt($_GET['module_id'])) {
    sendErrorResponse(422, 'Некорректный module_id');
}
$moduleId = (int)$_GET['module_id'];
$token = $_GET['access_token'];
Path::init($modulesByPathDeploy);

if (!in_array($moduleId, array(SVEDEN, ABITUR, VSOKO))) {
    sendErrorResponse(422, 'Не верно передан параметр модуля');
}

$moduleFolder = $modulesByPathDeploy[$moduleId];
$foldersNeedStay = $allowedFoldersInCoreByModule[$moduleId];

$moduleRootPath = Path::getModuleRootPath($moduleId);
$flagFileOfModulePath = Path::join($moduleRootPath, '.vikon');

try {
    if ($moduleId === ABITUR) {
        $headers = array('Accept-Encoding: zip, gzip', 'Authorization: Bearer ' . $token);
        $zipModuleCore = remoteRequest(
            $apiDomen . 'pull_updates/generateEmptyModuleCore/' . $moduleId,
            false,
            false,
            $headers
        );
        if ($zipModuleCore->curlHasError) {
            throw new RuntimeException('CURL ' . $zipModuleCore->curlErrorTxt);
        }

        if ($zipModuleCore->code !== 200) {
            $zipModuleCore->responseBody = json_decode($zipModuleCore->responseBody);
            $err = 'Не удается скачать файл с обновлениями. ' . (!empty($zipModuleCore->responseBody->message)
                    ? $zipModuleCore->responseBody->message
                    : 'Неизвестная ошибка');
            sendErrorResponse($zipModuleCore->code, $err);
        }

        $zipModuleCore->responseBody = json_decode($zipModuleCore->responseBody);
        if ($zipModuleCore->responseBody->success === true) {
            moduleDirIsEmptyOrEx($moduleRootPath, $flagFileOfModulePath);

            if (!Filesystem::safeMkdir($moduleRootPath, 0755, $moduleId)) {
                $err = 'Ошибка при распаковке ядра модуля. Не удалось создать папку: ' . $moduleFolder;
                sendErrorResponse(500, $err);
            }
            $filesDirectory = Path::join($moduleRootPath, 'files');
            if (!Filesystem::safeMkdir($filesDirectory, 0755, $moduleId)) {
                $err = 'Ошибка при распаковке ядра модуля. Не удалось создать папку: files';
                sendErrorResponse(500, $err);
            }
            $msg = 'Ядро модуля "' . $moduleFolder . '" успешно обновлено и синхронизировано.';
            if (!Filesystem::safeMkfile($flagFileOfModulePath, 0755)) {
                throw new Exception('Не удалось записать служебную информацию в директорию: ' . $flagFileOfModulePath);
            }
            sendSuccessResponse($msg);
        }
        sendSuccessResponse('Неизвестная ошибка');
    }

    $headers = array('Accept-Encoding: zip, gzip', 'Authorization: Bearer ' . $token);
    $zipModuleCore = remoteRequest(
        $apiDomen . 'pull_updates/generateEmptyModuleCore/' . $moduleId,
        false,
        false,
        $headers
    );
    if ($zipModuleCore->curlHasError) {
        throw new RuntimeException('CURL ' . $zipModuleCore->curlErrorTxt);
    }

    if ($zipModuleCore->code != 200) {
        $zipModuleCore->responseBody = json_decode($zipModuleCore->responseBody);
        $err = 'Не удается скачать файл с обновлениями. ' . (!empty($zipModuleCore->responseBody->message)
                ? $zipModuleCore->responseBody->message
                : 'Неизвестная ошибка');
        sendErrorResponse($zipModuleCore->code, $err);
    }

    moduleDirIsEmptyOrEx($moduleRootPath, $flagFileOfModulePath);

    $funcPath = Path::getFunctionalPath();
    $filenameZip = Path::join($funcPath, $moduleFolder . '_core-latest.zip');

    if (!Filesystem::removeZip($filenameZip, true)) {
        $err = 'Ошибка при распаковке ядра модуля. Архив уже существует. Недостаточно прав для его удаления';
        sendErrorResponse(500, $err);
    }

    $dlHandler = fopen($filenameZip, 'w');
    if (!fwrite($dlHandler, $zipModuleCore->responseBody)) {
        $err = 'Ошибка при распаковке ядра модуля. Не удалось записать архив. Проверьте права доступа и свободное место.';
        sendErrorResponse(500, $err);
    }

    $unpackNewModuleCorePath = Path::join($funcPath, $moduleFolder);
    if (!Filesystem::remove($unpackNewModuleCorePath, true)) {
        $err = 'Ошибка при распаковке ядра модуля. Временая директория уже существует и не может быть удалена.';
        sendErrorResponse(500, $err);
    }
    if (!unpackZip($filenameZip, $funcPath)) {
        $err = 'Ошибка при распаковке ядра модуля. Проверьте права доступа и свободное место.';
        sendErrorResponse(500, $err);
    }
    Filesystem::removeZip($filenameZip, false);

    // перед синхронизацией убедимся что папка модуля вообще существует
    if (!Filesystem::safeMkdir($moduleRootPath, 0755, $moduleId)) {
        $err = 'Ошибка обновлении ядра модуля. Не удалось создать папку модуля: ' . $moduleFolder;
        sendErrorResponse(500, $err);
    }

    $filesDirectory = Path::join($moduleRootPath, 'files');
    if (!Filesystem::safeMkdir($filesDirectory, 0755, $moduleId)) {
        $err = 'Ошибка обновлении ядра модуля. Не удалось создать папку в модуле: files';
        sendErrorResponse(500, $err);
    }

    $entriesToSync = Filesystem::safeScandir($unpackNewModuleCorePath);
    $serviceInfo = array('.vikon');
    $failedEntryName = null;
    foreach ($entriesToSync as $entryName) {
        if (in_array($entryName, $serviceInfo)) {
            continue;
        }
        $currentEntryPath = Path::join($moduleRootPath, $entryName);
        $newEntryPath = Path::join($unpackNewModuleCorePath, $entryName);
        $isFile = is_file($newEntryPath);

        if (file_exists($currentEntryPath)) {
            $newEntryPathWithNewPostfix = Path::join($moduleRootPath, $entryName . Path::$n_pstfx);
            // try del
            if (!Filesystem::remove($newEntryPathWithNewPostfix, true, $moduleId)) {
                $failedEntryName = $entryName;
                break;
            }
            // try replace
            $renameSuccess = !$isFile
                ? Filesystem::replaceWithRename($newEntryPath, $newEntryPathWithNewPostfix, $moduleId)
                : Filesystem::safeRenameFile($newEntryPath, $newEntryPathWithNewPostfix, $moduleId);
            if (!$renameSuccess) {
                $failedEntryName = $entryName;
                break;
            }

            $currentEntryPathWithOldPostfix = Path::join($moduleRootPath, $entryName . Path::$o_pstfx);
            // try del
            if (!Filesystem::remove($currentEntryPathWithOldPostfix, true, $moduleId)) {
                $failedEntryName = $entryName;
                break;
            }

            // try replace
            $renameSuccess = !$isFile
                ? Filesystem::replaceWithRename($currentEntryPath, $currentEntryPathWithOldPostfix, $moduleId)
                : Filesystem::safeRenameFile($currentEntryPath, $currentEntryPathWithOldPostfix, $moduleId);
            if (!$renameSuccess) {
                $failedEntryName = $entryName;
                break;
            }

            // try replace
            $renameSuccess = !$isFile
                ? Filesystem::replaceWithRename($newEntryPathWithNewPostfix, $currentEntryPath, $moduleId)
                : Filesystem::safeRenameFile($newEntryPathWithNewPostfix, $currentEntryPath, $moduleId);
            if (!$renameSuccess) {
                $failedEntryName = $entryName;
                break;
            }
        } else {
            // try replace
            $renameSuccess = !$isFile
                ? Filesystem::replaceWithRename($newEntryPath, $currentEntryPath, $moduleId)
                : Filesystem::safeRenameFile($newEntryPath, $currentEntryPath, $moduleId);
            if (!$renameSuccess) {
                $failedEntryName = $entryName;
                break;
            }
        }
    }

    if ($failedEntryName !== null) {
        if (!Filesystem::restoreUnitCoreAfterFail($moduleRootPath, $entriesToSync, $moduleId)) {
            $err = 'Ошибка при синхронизации ядра модуля. Не удалось восстановить ядро после ошибки.';
            sendErrorResponse(500, $err);
        } else {
            $err = 'Ошибка при синхронизации ядра модуля. Не удалось синхронизировать папку/файл: ' . $failedEntryName;
            sendErrorResponse(500, $err);
        }
    }

    Filesystem::remove($unpackNewModuleCorePath, true);

    $successOrPath = Filesystem::cleanUnitCore($moduleRootPath, $foldersNeedStay, $moduleId);
    if (is_string($successOrPath)) {
        $err = 'Ошибка проверки целостности ядра модуля. Не удалось удалить папку: ' . $successOrPath;
        sendErrorResponse(500, $err);
    }
    if (!$successOrPath) {
        $err = 'Ошибка проверки целостности ядра модуля. Нет доступа к корневой папке ядра.';
        sendErrorResponse(500, $err);
    }

    //todo new_core_after
    if ($moduleId === SVEDEN) {
        $updateDirModuleCore = Path::join($moduleRootPath, 'update');
        $oldUpdateFile = Path::join($moduleRootPath, 'update', 'index.php');
        Filesystem::safeMkdir($updateDirModuleCore, 0755, $moduleId);
        Filesystem::safeMkfile($oldUpdateFile, 0755);

        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $redirectPath = $locationOfVikonModules . 'vikon_core/update/index.php';
        $redirectUrl = $protocol . $domenName . $redirectPath;

        $indexContent = "<?php\n"
            . "header('Location: " . $redirectUrl . "');\n"
            . "exit;\n";
        file_put_contents($oldUpdateFile, $indexContent);
    }

    if (!Filesystem::safeMkfile($flagFileOfModulePath, 0755)) {
        throw new Exception('Не удалось записать служебную информацию в директорию: ' . $flagFileOfModulePath);
    }

} catch (Exception $ex) {
    sendErrorResponse(500, $ex->getMessage());
}
sendSuccessResponse('Ядро модуля "' . $moduleFolder . '" успешно обновлено и синхронизировано.');
