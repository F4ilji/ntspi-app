<?php

$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/config.php';
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/filesystem.php';

$token = filterAccessToken(isset($_POST['access_token']) ? (string) $_POST['access_token'] : '');
$moduleId = isset($_POST['moduleId']) ? filterInt($_POST['moduleId']) : null;
$dlHandler = null;

try {
    Path::init($modulesByPathDeploy);
    if (!in_array($moduleId, array(SVEDEN, ABITUR, VSOKO))) {
        throw new RuntimeException('Не верно передан параметр модуля');
    }

    if (!$token) {
        throw new RuntimeException('Некорректный access_token');
    }

    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
    $response = remoteRequest($filemanagerApiDomen . 'sync/getNewFileInfoByModule?moduleId=' . $moduleId, true, false, $headers);
    if ($response->curlHasError) {
        throw new RuntimeException('CURL ' . $response->curlErrorTxt);
    }

    if (
        $response->code !== 200
        || !property_exists($response->responseBody, 'file_name')
        || !property_exists($response->responseBody, 'identity')
        || !property_exists($response->responseBody, 'dir_name')
    ) {
        $msg = 'Не удалось получить информацию о файле, котрый требуется загрузить '
            . tryExtractFmErrorMessage($response, '. ');
        sendErrorResponse($response->code, $msg);
    }

    if ($response->responseBody->file_name === null && $response->responseBody->identity === null) {
        $resultBody = array(
            'success' => true,
            'forward_code' => 200,
            'done' => true,
        );
        loadHeaders();
        setResponseCode(200);
        echo json_encode($resultBody);
        die();
    }

    $fileIdentity = $response->responseBody->identity;
    $filename = $response->responseBody->file_name;
    $directory = $response->responseBody->dir_name;

    if (!Filesystem::ensureValidDirectoryAndFileName($directory, $filename)) {
        throw new RuntimeException('Некорректные параметры сохранения: недопустимое имя директории или файла.');
    }

    $headers = array('Accept-Encoding: zip, gzip', 'Authorization: Bearer ' . $token);
    $binFile = remoteRequest(
        $filemanagerApiDomen . 'sync/downloadFileBinary?identity=' . $fileIdentity,
        false,
        false,
        $headers
    );
    if ($binFile->curlHasError) {
        throw new RuntimeException('CURL ' . $binFile->curlErrorTxt);
    }

    if ($binFile->code !== 200) {
        $binFile->responseBody = json_decode($binFile->responseBody);
        $message = 'Не удается скачать файл ' . $filename . tryExtractFmErrorMessage($binFile, '. ');
        sendErrorResponse($binFile->code, $message);
    }

    $fsPathDir = Path::getFsPathByModule($moduleId);
    if ($directory !== null) {
        $fsPathDir = Path::join($fsPathDir, $directory);
    }

    if (!Filesystem::safeMkdir($fsPathDir, 0775, $moduleId)) {
        throw new RuntimeException('Не удалось создать папку "' . $fsPathDir . '" на вашем сервере');
    }

    $fsFilePath = Path::join($fsPathDir, $filename);
    $dlHandler = fopen($fsFilePath, 'w');
    if ($dlHandler == false || !fwrite($dlHandler, $binFile->responseBody)) {
        throw new RuntimeException('Не удается записать файл ' . $fsFilePath . ' на диск', 500);
    }

    $headers = array('Authorization: Bearer ' . $token);
    $response = remoteRequest(
        $filemanagerApiDomen . 'sync/markNewFileAsLoaded?identity=' . $fileIdentity . '&moduleId=' . $moduleId,
        true,
        false,
        $headers
    );
    if ($response->code !== 200) {
        $message = 'Не удалось пометить файл' . $filename . ' как обновленный'
            . tryExtractFmErrorMessage($response, '. ');
        sendErrorResponse($response->code, $message);
    }

    $resultBody = array(
        'success' => true,
        'forward_code' => 200,
        'message' => 'Файл ' . $filename . ' загружен',
        'done' => false,
    );
} catch (Exception $ex) {
    $resultBody = array('success' => false, 'forward_code' => 500, 'message' => $ex->getMessage());
}

if ($dlHandler) {
    fclose($dlHandler);
}

loadHeaders();
setResponseCode(200);
echo json_encode($resultBody);
