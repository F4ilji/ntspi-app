<?php

$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';

if (!isset($_POST['access_token']) || !filterAccessToken($_POST['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}

if (!isset($_POST['part']) || !filterPartName($_POST['part'])) {
    sendErrorResponse(422, 'Некорректный part');
}

$token = $_POST['access_token'];
$part = $_POST['part'];

try {
    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $token);

    $post = array('part' => $part, 'is_new_core' => true);
    $response = remoteRequest($apiDomen . 'pull_updates/generatePartByNewCoreJson', true, $post, $headers);
    if (!$response->curlHasError) {
        if (200 === $response->code) {
            $resultBody = array(
                'success' => true,
                'operation_identity' => $response->responseBody->operation_identity,
                'ttl' => $response->responseBody->ttl,
                'forward_code' => 200,
            );
        } else {
            $err = !empty($response->responseBody->message) ? $response->responseBody->message : 'Неизвестная ошибка';
            sendErrorResponse($response->code, $err);
        }
    } else {
        throw new RuntimeException('CURL ' . $response->curlErrorTxt);
    }
} catch (Exception $ex) {
    $resultBody = array('success' => false, 'forward_code' => 500, 'message' => $ex->getMessage());
}

loadHeaders();
setResponseCode(200);
echo json_encode($resultBody);
