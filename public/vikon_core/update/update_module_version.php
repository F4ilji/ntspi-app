<?php
$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';
require_once $updateDir . '/../internal/filesystem.php';


$token = null;
if (isset($_GET['access_token'])) { //todo new_core_after del GET
    $token = $_GET['access_token'];
} elseif (isset($_POST['access_token'])) {
    $token = $_POST['access_token'];
}

if ($token === null || !filterAccessToken($token)) {
    sendErrorResponse(422, 'Токен доступа отсутствует или некорректен');
}

$moduleId = null;
if (isset($_GET['module_id'])) { //todo new_core_after del GET
    $moduleId = $_GET['module_id'];
} elseif (isset($_POST['module_id'])) {
    $moduleId = $_POST['module_id'];
}

if ($moduleId === null || !filterInt($moduleId)) {
    sendErrorResponse(422, 'Отсутствует идентификатор модуля или он указан некорректно');
}
if (!in_array($moduleId, array(SVEDEN, ABITUR, VSOKO))) {
    sendErrorResponse(422, 'Не верно передан идентификатор модуля');
}

$version = null;
if (array_key_exists('version', $_GET)) { //todo new_core_after del GET
    $version = $_GET['version'];
} elseif (array_key_exists('version', $_POST)) {
    $version = $_POST['version'];
}

if ($version !== null && !filterVersion($version)) {
    sendErrorResponse(422, 'Передан невалидный параметр версии последнего обновления');
}

$headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
$res = remoteRequest($apiDomen . 'pull_updates/checkAccessJson', true, false, $headers);
if ($res->curlHasError) {
    sendErrorResponse(500, 'CURL ' . $res->curlErrorTxt);
}
if ($res->code !== 200) {
    $err = 'Ошибка проверки подлинности токена. '
        . (!empty($res->responseBody->message) ? $res->responseBody->message : 'Неизвестная ошибка');
    sendErrorResponse($res->code, $err);
}

try {
    $tmpPath = Path::join(Path::getCoreRootPath(), 'tmp');
    if (!Filesystem::safeMkdir($tmpPath, 0755)) {
        throw new RuntimeException('Не удалось создать директорию "' . $tmpPath . '" на вашем сервере');
    }

    $moduleVersionsPath = Path::join($tmpPath, 'versions');

    if (!Filesystem::safeMkdir($moduleVersionsPath, 0755)) {
        throw new RuntimeException('Не удалось создать директорию "' . $moduleVersionsPath . '" на вашем сервере');
    }
    $moduleVersionFilePath = Path::join($moduleVersionsPath, $moduleId . '.json');

    if (!Filesystem::remove($moduleVersionFilePath, false)) {
        throw new RuntimeException('Не удалось удалить файл "' . $moduleVersionFilePath . '" на вашем сервере');
    }

    $json = json_encode(array('version' => $version));
    if (!Filesystem::safeMkfile($moduleVersionFilePath, 0755, $json)) {
        throw new Exception('Не удалось записать служебную информацию в директорию: ' . $moduleVersionFilePath);
    }
} catch (Exception $ex) {
    sendErrorResponse(500, $ex->getMessage());
}

sendSuccessResponse('');
