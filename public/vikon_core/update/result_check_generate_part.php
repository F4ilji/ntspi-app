<?php

$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';

if (!isset($_POST['access_token']) || !filterAccessToken($_POST['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}

if (!isset($_POST['operation_identity']) || !filter_var($_POST['operation_identity'], FILTER_SANITIZE_STRING)) {
    sendErrorResponse(422, 'Некорректный operation_identity');
}

if (!isset($_POST['part']) || !filterPartName($_POST['part'])) {
    sendErrorResponse(422, 'Некорректный part');
}

$token = $_POST['access_token'];
$operationIdentity = (string) $_POST['operation_identity'];
$part = $_POST['part'];

try {
    $headers = array(
        'Accept: application/json',
        'Authorization: Bearer ' . $token,
    );

    $response = remoteRequest(
        $apiDomen . 'pull_updates/checkPartGenerationByNewCoreResultJson?operation_identity=' . $operationIdentity . '&part=' . $part,
        true,
        false,
        $headers
    );
    if (!$response->curlHasError) {
        if ($response->code === 200) {
            $resultBody = array(
                'success' => true,
                'forward_code' => 200,
            );
        } else {
            $resultBody = array(
                'success' => false,
                'forward_code' => $response->code,
                'message' => !empty($response->responseBody->message ) ? $response->responseBody->message : 'Неизвестная ошибка'
            );
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
