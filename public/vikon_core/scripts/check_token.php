<?php
$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/config.php';

$accessToken = filterAccessToken($_POST['access_token']);

$res = array();
try {
    if (!$accessToken) {
        throw new RuntimeException('Некорректный access_token');
    }
    $headers = array('Accept: application/json', 'Authorization: Bearer ' . $accessToken);
    $response = remoteRequest($apiDomainAuth . 'api/profile_applicant/check_access_token', true, false, $headers);
    if ($response->code == 200) {
        $res = $response->responseBody;
    } else {
        $err = 'Не удалось соединиться с удаленным сервером';
        sendErrorResponse($response->code, $err);
    }
} catch (Exception $e) {
    setResponseCode($e->getCode());
    $res['error'] = 'Ошибка при проверке токена. ' . $e->getMessage();
}
loadHeaders();
echo json_encode($res);
