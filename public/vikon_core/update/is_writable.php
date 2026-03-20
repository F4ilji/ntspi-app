<?php
$selfDir = dirname(__FILE__);
function isWritableR($dir)
{
    $message = '';
    if (is_dir($dir)) {
        if (is_writable($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    $message .= isWritableR($dir . DIRECTORY_SEPARATOR . $object);
                }
            }
        } else {
            $message .= 'Отсутствуют права на запись: "' . $dir . '"<br>';
        }
    } else if (!is_writable($dir)) {
        $message .= 'Отсутствуют права на запись: "' . $dir . '"<br>';
    }
    return $message;
}

$messageSveden = '';
$root = dirname($selfDir);
if (file_exists($root)) {
    $messageSveden = isWritableR($root);
}

if ($messageSveden) {
    echo '<p class="alert alert-danger">' . $messageSveden . '</p>';
}
