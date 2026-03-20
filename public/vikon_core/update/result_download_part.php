<?php

$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';
require_once $updateDir . '/../internal/filesystem.php';

if (!isset($_POST['access_token']) || !filterAccessToken($_POST['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}

if (!isset($_POST['operation_identity']) || !filter_var($_POST['operation_identity'], FILTER_SANITIZE_STRING)) {
    sendErrorResponse(422, 'Некорректный operation_identity');
}

if (!isset($_POST['part']) || !filterPartName($_POST['part'])) {
    sendErrorResponse(422, 'Некорректный part');
}

$dlHandler = null;
$token = $_POST['access_token'];
$operationIdentity = (string)$_POST['operation_identity'];
$part = $_POST['part'];
Path::init($modulesByPathDeploy);
try {
    $headers = array(
        'Authorization: Bearer ' . $token,
        'Accept-Encoding: zip, gzip'
    );

    $response = remoteRequest(
        $apiDomen . 'pull_updates/downloadPartByNewCoreResult?operation_identity=' . $operationIdentity . '&part=' . $part,
        false,
        false,
        $headers
    );

    if (!$response->curlHasError) {
        if ($response->code === 200) {
            $curModuleId = 0;
            foreach ($allowedFoldersInCoreByModule as $moduleId => $parts) {
                if (is_array($parts) && in_array($part, $parts)) {
                    $curModuleId = $moduleId;
                    break;
                }
            }

            $funcPath = Path::getFunctionalPath();
            $filenameZip = Path::join($funcPath, $operationIdentity . '.zip');

            if (!Filesystem::removeZip($filenameZip, true)) {
                $err = 'Ошибка при распаковке раздела: "' . $part . '".'
                    . ' Базовый архив раздела уже существует, недостаточно прав для его удаления.';
                sendErrorResponse(500, $err);
            }

            $dlHandler = fopen($filenameZip, 'w');
            if (!fwrite($dlHandler, $response->responseBody)) {
                $err = 'Ошибка при распаковке раздела: "' . $part . '".'
                    . ' Не удалось записать архив. Проверьте права доступа и свободное место.';
                sendErrorResponse(500, $err);
            }

            $unpackPath = Path::join($funcPath, $part.'_part-latest');
            if (!Filesystem::remove($unpackPath, true)) {
                $err = 'Ошибка при распаковке раздела: "' . $part . '".'
                    . ' Временная директория уже существует и не может быть удалена.';
                sendErrorResponse(500, $err);
            }

            if (!unpackZip($filenameZip, $unpackPath)) {
                $err = 'Ошибка при распаковке раздела: ' . $part . '.'
                    . ' Проверьте права доступа и свободное место.';
                sendErrorResponse(500, $err);
            }
            Filesystem::removeZip($filenameZip, false);

            $errRepeir = 'Ошибка при синхронизации раздела. Не удалось восстановить часть: ' . $part;
            $errSync = 'Ошибка при синхронизации раздела. Не удалось синхронизировать часть: ' . $part;

            $rootModuleCore = Path::getModuleRootPath($curModuleId);
            $pathCurFolderPart = Path::join($rootModuleCore, $part);
            $excludedEntries = array();
            if ('abitur' !== $part) {
                $excludedEntries = $allowedFoldersInCoreByModule[$curModuleId];
                $pathCurFolderPart = Path::join($rootModuleCore, $part);
                if (file_exists($pathCurFolderPart)) {
                    $pathPartForSync = Path::join($unpackPath, $part);
                    $pathPartForSyncPostfixNew = Path::join($rootModuleCore, $part.Path::$n_pstfx);

                    if (!Filesystem::remove($pathPartForSyncPostfixNew, true, $curModuleId)) {
                        if (!Filesystem::restoreUnitCoreAfterFail($rootModuleCore, array($part), $curModuleId)) {
                            sendErrorResponse(500, $errRepeir);
                        }
                    }
                    if (!Filesystem::replaceWithRename($pathPartForSync, $pathPartForSyncPostfixNew, $curModuleId)) {
                        if (!Filesystem::restoreUnitCoreAfterFail($rootModuleCore, array($part), $curModuleId)) {
                            sendErrorResponse(500, $errRepeir);
                        }
                        sendErrorResponse(500, $errSync);
                    }

                    $pathCurFolderPartOldPostfix = Path::join($rootModuleCore, $part.Path::$o_pstfx);
                    if (!Filesystem::remove($pathCurFolderPartOldPostfix, true, $curModuleId)) {
                        if (!Filesystem::restoreUnitCoreAfterFail($rootModuleCore, array($part), $curModuleId)) {
                            sendErrorResponse(500, $errRepeir);
                        }
                        sendErrorResponse(500, $errSync);
                    }
                    if (!Filesystem::replaceWithRename($pathCurFolderPart, $pathCurFolderPartOldPostfix, $curModuleId)) {
                        if (!Filesystem::restoreUnitCoreAfterFail($rootModuleCore, array($part), $curModuleId)) {
                            sendErrorResponse(500, $errRepeir);
                        }
                        sendErrorResponse(500, $errSync);
                    }

                    if (!Filesystem::replaceWithRename($pathPartForSyncPostfixNew, $pathCurFolderPart, $curModuleId)) {
                        if (!Filesystem::restoreUnitCoreAfterFail($rootModuleCore, array($part), $curModuleId)) {
                            sendErrorResponse(500, $errRepeir);
                        }
                        sendErrorResponse(500, $errSync);
                    }
                } else {
                    $pathPartForSync = Path::join($unpackPath, $part);
                    if (!Filesystem::replaceWithRename($pathPartForSync, $pathCurFolderPart, $curModuleId)) {
                        if (!Filesystem::restoreUnitCoreAfterFail($rootModuleCore, array($part), $curModuleId)) {
                            sendErrorResponse(500, $errRepeir);
                        }
                        sendErrorResponse(500, $errSync);
                    }
                }
            } else {
                $pathCurFolderPart = $rootModuleCore;
                $entrySyncFail = null;
                $isRestoreFail = false;
                $entriesToSync = Filesystem::safeScandir(Path::join($unpackPath, $part));
                $excludedEntries = array_merge($entriesToSync, array('files', '.htaccess'));//todo подумать как от этого костыля отказаться
                foreach ($entriesToSync as $entryName) {
                    $currentEntryPath = Path::join($pathCurFolderPart, $entryName);
                    $newEntryPath = Path::join($unpackPath, $part, $entryName);
                    $isFile = is_file($newEntryPath);

                    if (file_exists($currentEntryPath)) {
                        $newEntryPathWithNewPostfix = Path::join($pathCurFolderPart, $entryName.Path::$n_pstfx);
                        // try del
                        if (!Filesystem::remove($newEntryPathWithNewPostfix, true, $curModuleId)) {
                            $failedEntryName = $entryName;
                            break;
                        }
                        // try replace
                        $renameSuccess = !$isFile
                            ? Filesystem::replaceWithRename($newEntryPath, $newEntryPathWithNewPostfix, $curModuleId)
                            : Filesystem::safeRenameFile($newEntryPath, $newEntryPathWithNewPostfix, $curModuleId);
                        if (!$renameSuccess) {
                            $failedEntryName = $entryName;
                            break;
                        }
                        $currentEntryPathWithOldPostfix = Path::join($pathCurFolderPart, $entryName.Path::$o_pstfx);
                        // try del
                        if (!Filesystem::remove($currentEntryPathWithOldPostfix, true, $curModuleId)) {
                            $failedEntryName = $entryName;
                            break;
                        }

                        // try replace
                        $renameSuccess = !$isFile
                            ? Filesystem::replaceWithRename($currentEntryPath, $currentEntryPathWithOldPostfix, $curModuleId)
                            : Filesystem::safeRenameFile($currentEntryPath, $currentEntryPathWithOldPostfix, $curModuleId);
                        if (!$renameSuccess) {
                            $failedEntryName = $entryName;
                            break;
                        }

                        // try replace
                        $renameSuccess = !$isFile
                            ? Filesystem::replaceWithRename($newEntryPathWithNewPostfix, $currentEntryPath, $curModuleId)
                            : Filesystem::safeRenameFile($newEntryPathWithNewPostfix, $currentEntryPath, $curModuleId);
                        if (!$renameSuccess) {
                            $failedEntryName = $entryName;
                            break;
                        }
                    } else {
                        // try replace
                        $renameSuccess = !$isFile
                            ? Filesystem::replaceWithRename($newEntryPath, $currentEntryPath, $curModuleId)
                            : Filesystem::safeRenameFile($newEntryPath, $currentEntryPath, $curModuleId);
                        if (!$renameSuccess) {
                            $failedEntryName = $entryName;
                            break;
                        }
                    }
                }
            }

            Filesystem::remove($unpackPath, true);

            $successOrPath = Filesystem::cleanUnitCore($rootModuleCore, $excludedEntries, $curModuleId);
            if (is_string($successOrPath)) {
                $err = 'Ошибка проверки целостности части. Не удалось удалить папку: ' . $successOrPath;
                sendErrorResponse(500, $err);
            }
            if (!$successOrPath) {
                $err = 'Ошибка проверки целостности части. Нет доступа к корневой папке части.';
                sendErrorResponse(500, $err);
            }
            $resultBody = array('success' => true, 'forward_code' => 200, 'message' => 'Раздел ' . $part . ' успешно обновлен.');
        } else {
            $response->responseBody = json_decode($response->responseBody);

            $resultBody = array(
                'success' => false,
                'forward_code' => $response->code,
                'message' => !empty($response->responseBody->message ) ? $response->responseBody->message : 'неизвестная ошибка'
            );
        }
    } else {
        throw new RuntimeException('CURL ' . $response->curlErrorTxt);
    }
} catch (Exception $ex) {
    $resultBody = array('success' => false, 'forward_code' => 500, 'message' => $ex->getMessage());
}

if ($dlHandler != null) {
    fclose($dlHandler);
}

loadHeaders();
setResponseCode(200);
echo json_encode($resultBody);
