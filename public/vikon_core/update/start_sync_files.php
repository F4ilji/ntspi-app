<?php

$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/config.php';
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/filesystem.php';

$token = filterAccessToken(isset($_POST['access_token']) ? (string) $_POST['access_token'] : '');
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

    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
    $response = remoteRequest($filemanagerApiDomen . 'sync/getUsedDirNamesByModule?moduleId=' . $moduleId, true, false, $headers);

    if ($response->code !== 200) {
        $msg = 'Не удалось инициировать процедуру синхронизации файлов' . tryExtractFmErrorMessage($response, '. ');
        sendErrorResponse($response->code, $msg);
    }

    if (!property_exists($response->responseBody, 'directories') || !is_array($response->responseBody->directories)) {
        throw new RuntimeException('Невалидный формат ответа при запросе используемых директорий');
    }

    $directories = $response->responseBody->directories;
    $fsRootPath = Path::getFsPathByModule($moduleId);
    if (!$fsRootPath) {
        throw new RuntimeException('Неизвестная ошибка');
    }

    $dirObjects = Filesystem::safeScandir($fsRootPath);
    if (false === $dirObjects) {
        $msg = 'Не удалось просканировать папку: ' . $fsRootPath;
        sendErrorResponse(500, $msg);
    }

    $flippedKnownDirectories = array_flip($directories);
    foreach ($dirObjects as $objectName) {
        $objectPath = Path::join($fsRootPath, $objectName);
        if (
            !array_key_exists($objectName, $flippedKnownDirectories)
            && is_dir($objectPath)
        ) {
            if (is_link($fsRootPath)) {
                throw new RuntimeException(
                    'В синхронизируемый директории находится ссылка'
                    . $objectPath
                    . ' , которая не может быть безопасно удалена'
                );
            }
            Filesystem::remove($objectPath, true, $moduleId);
        }
    }

    $resultBody = array(
        'success' => true,
        'forward_code' => 200,
        'directories' => $directories,
    );
} catch (Exception $ex) {
    $resultBody = array('success' => false, 'forward_code' => 500, 'message' => $ex->getMessage());
}

loadHeaders();
setResponseCode(200);
echo json_encode($resultBody);
