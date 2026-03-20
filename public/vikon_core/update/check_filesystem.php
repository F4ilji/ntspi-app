<?php

$updateDir = dirname(__FILE__);
$rootDir = dirname($updateDir);

require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';
require_once $updateDir . '/../internal/filesystem.php';

$errPrefix = 'Для запуска инструмента не удалось подтвердить подлинность вашего токена доступа. ';

if (!isset($_POST['access_token']) || !filterAccessToken($_POST['access_token'])) {
    sendErrorResponse(422, $errPrefix . 'Токен доступа отсутствует или некорректен.');
}
$token = $_POST['access_token'];

try {
    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);
    $res = remoteRequest($apiDomen . 'pull_updates/checkAccessJson', true, false, $headers);
    if ($res->curlHasError) {
        sendErrorResponse(500, 'Ошибка CURL при выполнении запроса авторизации. ' . $errPrefix . $res->curlErrorTxt);
    }
    if ($res->code !== 200) {
        $err = $errPrefix . (!empty($res->responseBody->message) ? $res->responseBody->message : 'Неизвестная ошибка.');
        sendErrorResponse($res->code, $err);
    }

    Path::init($modulesByPathDeploy);
    $payload = array(
        'is_access_allowed' => true,
        'err' => null
    );
    $message = isWritableRecrusive($rootDir);
    if ($message) {
        $payload['is_access_allowed'] = false;
        $payload['err'] = $message;
    }

    loadHeaders();
    setResponseCode(200);
    echo json_encode(
        array(
            'success' => true,
            'forward_code' => 200,
            'payload' => $payload
        )
    );
    die();
} catch (Exception $e) {
    sendErrorResponse(500, $e->getMessage());
}

function isWritableRecrusive($dir)
{
    $message = '';
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $objectPath = Path::join($dir, $object);
                    $message .= isWritableRecrusive($objectPath);
                }
            }
        } else {
            $message .= 'Отсутствуют права на запись: "' . htmlspecialchars($dir) . '"<br>';
        }
    } else {
        if (!is_writable($dir)) {
            $message .= 'Отсутствуют права на запись: "' . htmlspecialchars($dir) . '"<br>';
        }
    }
    return $message;
}
