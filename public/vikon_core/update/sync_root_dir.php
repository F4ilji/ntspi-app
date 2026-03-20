<?php

$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/config.php';
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/filesystem.php';

$token = filterAccessToken(isset($_POST['access_token']) ? (string) $_POST['access_token'] : '');
//через jquery нельзя послать пустой массив, если это не json.
// json посылать не будем, похоже при некоторых настройках старых серверов есть проблемы с чтением raw-body через php://input($HTTP_RAW_POST_DATA)
$isHasSubDirs = isset($_POST['isHasSubDirs']) && is_numeric($_POST['isHasSubDirs'])
    ? (int) $_POST['isHasSubDirs']
    : null;
$knownSubDirs = isset($_POST['knownSubDirs']) && is_array($_POST['knownSubDirs'])
    ? $_POST['knownSubDirs']
    : array();
$moduleId = isset($_POST['moduleId']) ? filterInt($_POST['moduleId']) : null;

$dlHandler = null;
Path::init($modulesByPathDeploy);

try {
    if (!in_array($moduleId, array(SVEDEN, ABITUR, VSOKO))) {
        throw new RuntimeException('Не верно передан параметр модуля');
    }

    if (!$token) {
        throw new RuntimeException('Некорректный access_token');
    }

    if (null === $isHasSubDirs || ($isHasSubDirs && !$knownSubDirs) || (!$isHasSubDirs && $knownSubDirs)) {
        throw new RuntimeException('Не может быть обработано. Неверные параметры запроса.');
    }

    $fsDirPath = Path::getFsPathByModule($moduleId);
    if (!$fsDirPath) {
        throw new RuntimeException('Не удалось определить модуль');
    }

    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
    $response = remoteRequest($filemanagerApiDomen . 'sync/getFileNamesFromRootDirectoryByModule?moduleId=' . $moduleId, true, false, $headers);
    if ($response->curlHasError) {
        throw new RuntimeException('CURL ' . $response->curlErrorTxt);
    }

    if (
        $response->code !== 200
        || !property_exists($response->responseBody, 'files')
        || !is_array($response->responseBody->files)
    ) {
        $msg = 'Не удалось получить список файлов с файлового сервера'
            . tryExtractFmErrorMessage($response, '. ');
        sendErrorResponse($response->code, $msg);
    }
    $filesByNamesFromFm = array();
    foreach ($response->responseBody->files as $row) {
        $filesByNamesFromFm[(string) $row->n] = $row->i;
    }
    $response = null;

    if (!Filesystem::safeMkdir($fsDirPath, 0775, $moduleId)) {
        throw new RuntimeException('Не удалось создать папку "' . $fsDirPath . '" на вашем сервере');
    }
    $items = Filesystem::safeScandir($fsDirPath);
    if (false === $items) {
        $msg = 'Не удалось просканировать существующие файлы';
        sendErrorResponse(500, $msg);
    }

    $existingItemsByNames = array();
    foreach ($items as $fName) {
        $existingItemsByNames[$fName] = null;
    }

    $knownSubDirsByNames = array_flip($knownSubDirs);
    foreach ($existingItemsByNames as $dirItem => $_) {
        $fsItemPath = Path::join($fsDirPath, $dirItem);
        if ((array_key_exists($dirItem, $knownSubDirsByNames) && is_dir($fsItemPath))) {
            unset($existingItemsByNames[$dirItem]);
            continue;
        }

        if (!array_key_exists((string) $dirItem, $filesByNamesFromFm)) {
            //удаляем только файлы, ненужные папки были удалены в start_sync_files
            if (is_file($fsItemPath)) {
                unlink($fsItemPath);
            }
        } else {
            if (!filesize($fsItemPath)) {
                unset($existingItemsByNames[$dirItem]);
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
