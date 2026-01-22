<?php
$selfDir = dirname(__FILE__);
require_once $selfDir . '/../internal/helper.php';
require_once $selfDir . '/../internal/config.php';

$res = array();
$res['success'] = true;
$res['errorID'] = array();
$res['messages'] = array();

$name = $_POST['name'];
$email = $_POST['email'];
$message = $_POST['message'];
$consent = filter_var($_POST['consent'], FILTER_SANITIZE_NUMBER_INT);

if($consent != true){
    $res['success'] = false;
    $res['messages'][] = 'Не получено согласие на обработку персональных данных.';
    $res['errorID'][] = 'consent';
}

$code = filter_var($_POST['captcha'], FILTER_SANITIZE_STRING);
session_start();
if (!isset($_SESSION['captcha']) || strtoupper(trim($_SESSION['captcha'])) != strtoupper(trim($code))) {
    $res['success'] = false;
    $res['errorID'][] = 'captcha';
    $res['messages'][] = 'Неверный код с картинки.';
}
unset($_SESSION['captcha']);

if ($res['success']) {
    $res['success'] = false;

    $headers = array('Accept: application/json');
    $data = remoteRequest($apiDomen . 'oauth2/ClientCredentials', true,
        array(
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials',
        ), $headers
    );

    if ($data->code == 200) {
        if (isset($data->responseBody)) {
            $accessToken = $data->responseBody->access_token;

            try {
                $data = remoteRequest($apiDomen . 'oauth-via-app/vsoko/feedbackSendMail?access_token='
                    . $accessToken .
                    '&name=' . urlencode($name) .
                    '&email=' . urlencode($email) .
                    '&message=' . urlencode($message)
                );

                if ($data->code == 200) {
                    $res['success'] = true;
                } else {
                    sendErrorResponse($data->code, $data->responseBody->message);
                }
            } catch (Exception $e) {
                $res['message'] = 'Ошибка. '.$e->getMessage();
            }

        } else {
            $res['message'] = 'Ошибка ' . $data->responseBody->message;
        }
    } else {
        $res['message'] = $data->responseBody->message;
    }
}

loadHeaders();

echo json_encode($res);
