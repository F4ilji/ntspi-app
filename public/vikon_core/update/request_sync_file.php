<?php

$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/config.php';
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/filesystem.php';

$token = filterAccessToken(isset($_POST['access_token']) ? (string) $_POST['access_token'] : '');

$fileIdentity = isset($_POST['fileIdentity']) && is_scalar($_POST['fileIdentity']) ? $_POST['fileIdentity'] : null;
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

    if (!$fileIdentity) {
        throw new RuntimeException('Передан невалидный идентификатор файла');
    }

    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
    $response = remoteRequest(
        $filemanagerApiDomen . 'sync/getFileByIdentityInfo?identity=' . $fileIdentity,
        true,
        false,
        $headers
    );
    if ($response->curlHasError) {
        throw new RuntimeException('CURL ' . $response->curlErrorTxt);
    }

    if (
        $response->code !== 200
        || !property_exists($response->responseBody, 'file_name')
        || !property_exists($response->responseBody, 'identity')
        || !property_exists($response->responseBody, 'dir_name')
    ) {
        $resultBody = array(
            'success' => false,
            'forward_code' => $response->code == 200 ? 500 : $response->code,
            'message' => (int) $response->code === 404
                ? 'Не удалось получить информацию об одном из синхронизируемых файлов. Файл был удален из системы после запуска процесса синхронизации. Перезапустите синхронизацию файлов.'
                : 'Не удалось получить информацию о файле, котрый требуется загрузить ' . tryExtractFmErrorMessage($response, '. '),
            'debug_identity' => $fileIdentity,
        );
        loadHeaders();
        setResponseCode(200);
        echo json_encode($resultBody);
        die();
    }

    $identity = $response->responseBody->identity;
    $filename = $response->responseBody->file_name;
    $directory = $response->responseBody->dir_name;

    if (!Filesystem::ensureValidDirectoryAndFileName($directory, $filename)) {
        throw new RuntimeException('Некорректные параметры сохранения: недопустимое имя директории или файла.');
    }

    $headers = array('Accept-Encoding: zip, gzip', 'Authorization: Bearer ' . $token);
    $bin = remoteRequest(
        $filemanagerApiDomen . 'sync/downloadFileBinaryForSync?identity=' . $identity . '&moduleId=' . $moduleId,
        false,
        false,
        $headers
    );
    if ($bin->curlHasError) {
        throw new RuntimeException('CURL ' . $bin->curlErrorTxt);
    }

    if ($bin->code !== 200) {
        $bin->responseBody = json_decode($bin->responseBody);
        $resultBody = array(
            'success' => false,
            'forward_code' => $bin->code,
            'message' => 'Не удается скачать файл ' . $filename . tryExtractFmErrorMessage($bin, '. '),
            'debug_identity' => $fileIdentity,
        );
        loadHeaders();
        setResponseCode(200);
        echo json_encode($resultBody);
        die();
    }

    $fsPathDir = Path::getFsPathByModule($moduleId);
    if (!$fsPathDir) {
        throw new RuntimeException('Неизвестная ошибка');
    }

    if ($directory !== null) {
        $fsPathDir = Path::join($fsPathDir, $directory);
    }

    if (!Filesystem::safeMkdir($fsPathDir, 0775, $moduleId)) {
        throw new RuntimeException('Не удалось создать папку "' . $fsPathDir . '" на вашем сервере');
    }

    $filePath = Path::join($fsPathDir, $filename);

    $dlHandler = fopen($filePath, 'w');
    if ($dlHandler === false) {
        throw new RuntimeException('Не удается открыть файл для записи: ' . $filePath);
    }

    if (fwrite($dlHandler, $bin->responseBody) === false) {
        throw new RuntimeException('Не удается записать файл на диск: ' . $filePath);
    }

    $resultBody = array(
        'success' => true,
        'forward_code' => 200,
        'message' => 'Файл ' . $filename . ' загружен',
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
