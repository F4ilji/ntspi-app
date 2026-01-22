<?php
$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/config.php';
require_once $selfDir . '/../internal/helper.php';

$res = array();
$res['success'] = false;

if (!isset($_POST['access_token']) || !filterAccessToken($_POST['access_token'])) {
    sendErrorResponse(422, 'Некорректный access_token');
}

try {
    $headers = array(
        'Accept: application/json',
        'Authorization: Bearer ' . $token = $_POST['access_token'],
    );

    $response = remoteRequest($apiDomen . 'pull_updates/assist/updateEndedSuccessByNewCoreJson', true, array(), $headers);
    if (!$response->curlHasError) {
        if (200 === $response->code) {
            $resultBody = array(
                'success' => true,
                'forward_code' => 200,
            );
        } else {
            $resultBody = array(
                'success' => false,
                'forward_code' => $response->code,
                'message' => !empty($response->responseBody->message )
                    ? $response->responseBody->message
                    : 'Неизвестная ошибка'
            );
        }
    } else {
        throw new RuntimeException( 'CURL ' . $response->curlErrorTxt);
    }
} catch (Exception $ex) {
    $resultBody = array('success' => false, 'forward_code' => 500, 'message' => $ex->getMessage());
}

loadHeaders();
setResponseCode(200);
echo json_encode($resultBody);
