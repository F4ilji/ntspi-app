<?php
$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';

if (!isset($_POST['refresh_token']) || !filterAccessToken($_POST['refresh_token'])) {
    sendErrorResponse(422, 'Некорректный code');
}

$res = array();
$res['success'] = false;
$refreshToken = filterAccessToken($_POST['refresh_token']);

try {
    $headers = array('Accept: application/json');
    $data = remoteRequest($apiDomen . 'oauth2/RefreshToken', true, array(
        'refresh_token' => $refreshToken,
        'client_id' => $clientId,
        'client_secret' => $clientSecret,
        'grant_type' => 'refresh_token',
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
    $res['message'] = 'Ошибка при обновлении ключа. ' . $e->getMessage();
    setResponseCode(500);
}

loadHeaders();
echo json_encode($res);

