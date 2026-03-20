<?php

$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';
require_once $updateDir . '/../internal/filesystem.php';

if (!isset($_GET['access_token']) || !filterAccessToken($_GET['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}

$token = $_GET['access_token'];

$dlHandler = null;

try {
    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
    $hasAccess = remoteRequest($apiDomen . 'pull_updates/checkAccessJson', true, false, $headers);
    if ($hasAccess->curlHasError) {
        throw new RuntimeException('CURL ' . $hasAccess->curlErrorTxt);
    }

    if ($hasAccess->code !== 200) {
        $err = 'Не удается скачать файл с обновлениями. ' . (!empty($hasAccess->responseBody->message)
                ? $hasAccess->responseBody->message
                : 'Неизвестная ошибка');
        sendErrorResponse($hasAccess->code, $err);
    }

    if ($hasAccess->responseBody->success) {
        $vikonRootPath = Path::getCoreRootPath();
        $executorPath = Path::join($vikonRootPath, Path::$executorFile);

        if (!Filesystem::remove($executorPath, false)) {
            sendErrorResponse(500, 'Ошибка при генерации ядра. Не удалось удалить устаревший исполняемый скрипт: ' . $executorPath);
        }

        $srcForExecutorPath = Path::getSrcForExecutorPath();
        $executorCode = file_get_contents($srcForExecutorPath);
        if (!$executorCode || !Filesystem::safeMkfile($executorPath, 0755, $executorCode)) {
            sendErrorResponse(500, 'Ошибка при генерации ядра. Не удалось создать исполняемый скрипт: ' . $executorPath);
        }
        sendSuccessResponse('');
    }
} catch (Exception $ex) {
    sendErrorResponse(500, $ex->getMessage());
}
if ($dlHandler) {
    fclose($dlHandler);
}
