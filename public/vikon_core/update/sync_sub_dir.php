<?php

$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/config.php';
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/filesystem.php';

$dir = isset($_POST['dir']) && is_string($_POST['dir']) ? $_POST['dir'] : '';
$token = filterAccessToken(isset($_POST['access_token']) ? (string) $_POST['access_token'] : '');
$moduleId = isset($_POST['moduleId']) ? filterInt($_POST['moduleId']) : null;

$dlHandler = null;
Path::init($modulesByPathDeploy);

try {
    if (!preg_match("/^[a-z]{3,4}$/", $dir)) {
        throw new RuntimeException('Невалидное значение для синхронизируемой суб-директории');
    }

    if (!$token) {
        throw new RuntimeException('Некорректный access_token');
    }

    if (!in_array($moduleId, array(SVEDEN, ABITUR, VSOKO))) {
        throw new RuntimeException('Не верно передан параметр модуля');
    }

    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
    $url = $filemanagerApiDomen . 'sync/getFileNamesFromSubDirectoryByModule?dir=' . $dir . '&moduleId=' . $moduleId;
    $response = remoteRequest($url, true, false, $headers);
    if ($response->curlHasError) {
        throw new RuntimeException('CURL ' . $response->curlErrorTxt);
    }
    if (
        $response->code !== 200
        || !property_exists($response->responseBody, 'files')
        || !is_array($response->responseBody->files)
    ) {
        $message = 'Не удалось получить список файлов с файлового сервера'
            . tryExtractFmErrorMessage($response, '. ');
        sendErrorResponse($response->code, $message);
    }

    $filesByNamesFromFm = array();
    foreach ($response->responseBody->files as $row) {
        $filesByNamesFromFm[(string) $row->n] = $row->i;
    }
    $response = null;

    $fsDirPath = Path::getFsPathByModule($moduleId);
    if (!$fsDirPath) {
        throw new RuntimeException('Не удалось определить модуль');
    }

    $dirPath = Path::join($fsDirPath, $dir);
    if (!Filesystem::safeMkdir($dirPath, 0775, $moduleId)) {
        throw new RuntimeException('Не удалось создать папку "' . $dirPath . '" на вашем сервере');
    }

    $items = Filesystem::safeScandir($dirPath);
    if (false === $items) {
        $msg = 'Не удалось просканировать существующие файлы';
        sendErrorResponse(500, $msg);
    }

    $existingItemsByNames = array();
    foreach ($items as $fName) {
        $existingItemsByNames[$fName] = null;
    }

    foreach ($existingItemsByNames as $subDirItem => $_) {
        $filePath = Path::join($dirPath, $subDirItem);
        if (!array_key_exists((string) $subDirItem, $filesByNamesFromFm)) {
            if (is_dir($filePath)) {
                Filesystem::remove($filePath, true);
            } else {
                unlink($filePath);
            }
        } else {
            if (!filesize($filePath)) {
                unset($existingItemsByNames[$subDirItem]);
            }
        }
    }

    $filesForSync = array();
    foreach ($filesByNamesFromFm as $fileName => $identity) {
        if (!array_key_exists($fileName, $existingItemsByNames)) {
            $filesForSync[] = $identity;
        }
    }

    $resultBody = array('success' => true, 'forward_code' => 200, 'files' => $filesForSync);
} catch (Exception $ex) {
    $resultBody = array('success' => false, 'forward_code' => 500, 'message' => $ex->getMessage());
}

loadHeaders();
setResponseCode(200);
echo json_encode($resultBody);
