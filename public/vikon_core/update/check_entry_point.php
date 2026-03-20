<?php

$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';

if (!isset($_GET['entry']) && filterEntry($_GET['entry'])) {
    sendErrorResponse(422, 'Некорректный entry');
}

$entry = $_GET['entry'];

try {
    $headers = array('Accept: application/json');
    $result = remoteRequest($apiDomen . 'oauth2/checkEntryPoint?client_id=' . $clientId . '&entry_point=' . $entry, true, false, $headers);
    if ($result->curlHasError) {
        sendErrorResponse(500, 'Не удалось соединиться с сервером.' . $result->curlErrorTxt);
    } else {
        setResponseCode($result->code);
    }
    loadHeaders();
    echo json_encode(array('success' => $result->responseBody->success));

} catch (Exception $e) {
    sendErrorResponse(500, 'Не удалось проверить настройки безопасности.' . $e->getMessage());
}
