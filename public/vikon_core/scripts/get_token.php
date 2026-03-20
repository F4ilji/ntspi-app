<?php
$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/config.php';

$login = filterLogin($_POST['login']);
$password = filterPassword($_POST['password']);
$code = filterInt($_POST['code']);

$res = array();
try {
    if (!$login && !$password && !$code) {
        throw new RuntimeException('Переданы некорректные параметры');
    }
    $postFields = array(
        'grant_type' => 'password',
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'username' => $login,
        'password' => $password,
        'code' => $code,
        'scope' => '',
    );
    $response = remoteRequest($apiDomainAuth . 'oauth/token', true, $postFields, array(
        'App-Language: ' . isset($_POST['lang']) ? $_POST['lang'] : 'ru'
    ));
    if ($response->code == 200) {
        setResponseCode($response->code);
        $res = $response->responseBody;
    } else {
        $err = 'Не удалось соединиться с удаленным сервером.';
        sendErrorResponse($response->code, $err);
    }
} catch (Exception $e) {
    setResponseCode($e->getCode());
    $res['error'] = 'Ошибка при получении токена. ' . $e->getMessage();
}
loadHeaders();
echo json_encode($res);
