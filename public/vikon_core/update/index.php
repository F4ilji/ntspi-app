<?php
$code = isset($_GET['code']) ? $_GET['code'] : '';
$state = isset($_GET['state']) ? $_GET['state'] : '';

$query = http_build_query(array_filter([
    'code'  => $code,
    'state' => $state,
]));

$redirectUrl = '/dashboard/vikon-updates/callback' . ($query ? '?' . $query : '');

header('Location: ' . $redirectUrl, true, 302);
exit;
