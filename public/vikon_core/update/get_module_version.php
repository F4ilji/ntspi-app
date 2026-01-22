<?php
$updateDir = dirname(__FILE__);
require_once $updateDir . '/../internal/config.php';
require_once $updateDir . '/../internal/helper.php';
require_once $updateDir . '/../internal/filesystem.php';

if (!isset($_GET['module_id']) || !filterInt($_GET['module_id'])) {
    sendErrorResponse(422, 'Отсутствует идентификатор модуля или он указан некорректно');
}
$moduleId = (int) $_GET['module_id'];
if (!in_array($moduleId, array(SVEDEN, ABITUR, VSOKO))) {
    sendErrorResponse(422, 'Не верно передан идентификатор модуля');
}
$tmpDir = Path::getTmpVersionPath();
$result = null;

$filePath = Path::join($tmpDir, $moduleId . '.json');
if (!file_exists($filePath) || !is_readable($filePath)) {
    sendErrorResponse(422, 'Не получить файл с версией');
}
$json = @file_get_contents($filePath);
if ($json === false || $json === '') {
    sendErrorResponse(422, 'Не удалось получить версию');
}
$data = @json_decode($json, true);

loadHeaders();
echo json_encode(array(
    'success' => true,
    'message' => '',
    'forward_code' => 200,
    'version' => isset($data['version']) ? htmlspecialchars($data['version']) : null
));
