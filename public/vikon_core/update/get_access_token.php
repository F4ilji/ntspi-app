<?php
$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';

$res = array();
$res['success'] = false;

$codeSuccess = isset($_POST['code']) && filterAccessToken($_POST['code']);
$urlSuccess = isset($_POST['url']) && filterUrl($_POST['url']);

if (!$codeSuccess) {
    sendErrorResponse(422, 'Некорректный авторизационный код');
}

if (!$urlSuccess) {
    sendErrorResponse(422, 'Некорректный url выполнения запроса');
}

try {
    $headers = array('Accept: application/json');
    $data = remoteRequest($apiDomen . 'oauth2/authorize/token', true, array(
        'code' => $_POST['code'],
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'redirect_uri' => $_POST['url'],
        'grant_type' => 'authorization_code',
    ), $headers);

    if ($data->curlHasError) {
        $res['message'] = 'Не удалось соединиться с сервером.' . $data->curlErrorTxt;
        setResponseCode(500);
    } else {
        if (isset($data->responseBody->access_token)) {
            $res['access_token'] = $data->responseBody->access_token;
            $res['refresh_token'] = $data->responseBody->refresh_token;
            $res['success'] = true;
        } else {
            $message = '';
            if (isset($data->responseBody->message)) {
                $message = $data->responseBody->message;
            } else {
                $message = 'Ошибка при получении токена.';
            }
            $res['message'] = $message;
            setResponseCode($data->code);
        }
    }

} catch (Exception $e) {
    $res['message'] = 'Ошибка при получении ключа. ' . $e->getMessage();
    setResponseCode(500);
}

loadHeaders();
echo json_encode($res);
