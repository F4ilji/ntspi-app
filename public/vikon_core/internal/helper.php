<?php
function loadHeaders()
{
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    header('Content-type: application/json');
}

function sendErrorResponse($code, $message)
{
    $resultBody = array(
        'success' => false,
        'forward_code' => $code,
        'message' => $message,
    );
    loadHeaders();
    setResponseCode(200);
    echo json_encode($resultBody);
    die();
}

function sendSuccessResponse($message)
{
    $resultBody = array(
        'success' => true,
        'forward_code' => 200,
        'message' => $message,
    );

    loadHeaders();
    setResponseCode(200);
    echo json_encode($resultBody);
    die();
}

// unpackZip плохо проверяет может ли он распаковать в этой папке
// перед использованием сделай Filesystem::remove($pathToUnpack) - чтобы убедиться что проблем не будет
function unpackZip($fileName, $pathToUnpack)
{
    $res = array();
    $res['success'] = false;
    $res['message'] = '';
    if($pathToUnpack) {
        if (extension_loaded('zip')) {
            $zip = new ZipArchive;
            if ($isOpenedZip = $zip->open($fileName)) {
                $zip->extractTo($pathToUnpack);
                $zip->close();
                $res['success'] = true;

            } else {
                $res['message'] = 'Ошибка при распоковке файлов библиотеки ZIP:' . $isOpenedZip;
            }
        } else {
            require_once dirname(__FILE__) . '/../update/zip_helper.php';
            $archive = new PclZip($fileName);
            $unZipResult = $archive->extract(PCLZIP_OPT_PATH, $pathToUnpack,
                PCLZIP_CB_PRE_EXTRACT, 'preExtractCallback', PCLZIP_OPT_REPLACE_NEWER);
            if ($unZipResult == 0) {
                $res['message'] = 'Ошибка при распоковке файлов:' . $archive->errorInfo(true);
            } else {
                $res['success'] = true;
            }
            $archive->privCloseFd();
        }
    }
    return $res;
}

function filterAccessToken($token)
{
    return filter_var($token, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[a-z0-9_\-\.]+$/i')));
}

function filterFileName($file)
{
    return filter_var($file, FILTER_VALIDATE_REGEXP,
        array('options' => array('regexp' => '/^[a-z0-9\+\.\,\(\)\;\#\№\-\_\«\»\!\%\=\$\@\'\&\–]+$/i'))
    );
}

function filterPath($file)
{
    return filter_var($file, FILTER_VALIDATE_REGEXP,
        array('options' => array('regexp' => '/^[a-z0-9\.\-\_\–\/]+$/i'))
    );
}

function filterEntry($entry)
{
    return filter_var($entry, FILTER_VALIDATE_REGEXP,
        array('options' => array('regexp' => '/^\/(?:[a-zA-Zа-яА-Я0-9_\-\.]+(?:\/)?)+$/'))
    );
}

function filterInt($number)
{
    return filter_var($number, FILTER_VALIDATE_INT);
}

function filterUrl($url)
{
    return filter_var($url, FILTER_VALIDATE_URL);
}

function filterVersion($version)
{
    return filter_var($version, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[0-9\.]+$/')));
}

function filterPartName($part)
{
    return filter_var($part, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => '/^[a-z_\-]+$/i')));
}

function filterLogin($str)
{
    return filter_var($str, FILTER_VALIDATE_EMAIL);
}

function filterPassword($str)
{
    return filter_var($str, FILTER_SANITIZE_STRING);
}

function setResponseCode($code, $reason = null) {
    $code = intval($code);

    if (version_compare(phpversion(), '5.4', '>') && is_null($reason)) {
        http_response_code($code);
    } else {
        header(trim("HTTP/1.0 $code $reason"));
    }
}

function preExtractCallback($preEvent, &$preHeader)
{
    if (!is_dir($preHeader['filename'])) {
        if (strpos($preHeader['filename'], '.htaccess')) {
            return 0;
        }
        if (file_exists($preHeader['filename'])) {
            unlink($preHeader['filename']);
        }
    }
    return 1;
}

class RemoteResult
{
    public $curlHasError = true;
    public $curlErrorTxt = '';

    public $code = 0;
    public $responseBody = '';
}

function remoteRequest($url, $responseAsJson = true, $postFields = false, $headers = array())
{
    $result = new RemoteResult();
    $ch = curl_init();
    if (IS_DOMAIN_RESOLVE === 1) {
        curl_setopt($ch, CURLOPT_RESOLVE, array(
            VIKON_DOMAIN_RESOLVE_BYPASS,
            FM_DOMAIN_RESOLVE_BYPASS,
        ));
    }
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

    if (is_array($postFields)) {
        curl_setopt($ch, CURLOPT_POST, true);
        $postVars = "";
        foreach ($postFields as $key => $value) {
            $postVars .= $key . '=' . $value . '&';
        }
        $postVars = rtrim($postVars, '&');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postVars);
    }

    if ($headers !== array()) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $output = curl_exec($ch);

    if (curl_errno($ch) > 0 || curl_error($ch)) {
        $result->curlErrorTxt .= ';' . '#:' . curl_errno($ch) . 'Error:' . curl_error($ch);
    } else{
        $result->curlHasError = false;

        $curlInfo = curl_getinfo($ch);
        $result->code = (int) $curlInfo['http_code'];
        $result->responseBody = ($responseAsJson) ? json_decode($output) : $output;
    }
    curl_close($ch);
    return $result;
}

function tryExtractFmErrorMessage($remoteResultFmWhereBodyIsJson, $prefix = '')
{
    $out = '';
    if (property_exists($remoteResultFmWhereBodyIsJson->responseBody, 'error')) {
        $out = $out . $remoteResultFmWhereBodyIsJson->responseBody->error;
    }

    if (
        property_exists($remoteResultFmWhereBodyIsJson->responseBody, 'messages')
        && is_array($remoteResultFmWhereBodyIsJson->responseBody->messages)
    ) {
        $message = version_compare(phpversion(), '8.0', '>=')
            ? implode('; ', $remoteResultFmWhereBodyIsJson->responseBody->messages)
            : implode($remoteResultFmWhereBodyIsJson->responseBody->messages, '; ');
        $out = $out . $message;
    }

    if ($out) {
        $out = $prefix . 'Файловый сервер: ' . $out;
    }

    return $out;
}

function moduleDirIsEmptyOrEx($moduleRootPath, $flagFileOfModulePath)
{
    if (is_dir($moduleRootPath)) {
        if (count(Filesystem::safeScandir($moduleRootPath)) && !file_exists($flagFileOfModulePath)) {
            throw new Exception(
                'Ошибка при распаковке ядра модуля. Целевая директория: ' . $moduleRootPath . ' уже содержит данные. ' .
                'Для запуска обновления целевая директория должна быть пуста. ' .
                'Удалите или переместите существующие данные и повторите обновление.'
            );
        }
    }
}
