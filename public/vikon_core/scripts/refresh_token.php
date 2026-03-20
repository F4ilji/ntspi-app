<?php
$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/config.php';

$refreshToken = filterAccessToken($_POST['refresh_token']);

$res = array();
try {
    if (!$refreshToken) {
        throw new RuntimeException('Некорректный refresh_token');
    }
    $postFields = array(
        'grant_type' => 'refresh_token',
        'refresh_token' => $refreshToken,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'scope' => '',
    );
    $response = remoteRequest($apiDomainAuth . 'oauth/token', true, $postFields);
    if ($response->code == 200) {
        $res = $response->responseBody;
    } else {
        $err = 'Не удалось соединиться с удаленным сервером';
        sendErrorResponse($response->code, $err);
    }
} catch (Exception $e) {
    setResponseCode($e->getCode());
    $res['error'] = 'Ошибка при обновлении токена. ' . $e->getMessage();
}
loadHeaders();
echo json_encode($res);
