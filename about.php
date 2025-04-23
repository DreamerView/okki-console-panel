<?php
function formatSize($bytes) {
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB'];
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.2f", $bytes / pow(1024, $factor)) . " " . $sizes[$factor];
}

function getDirectorySize($dir) {
    $size = 0;
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file == '.' || $file == '..') {
            continue;
        }

        $filePath = $dir . DIRECTORY_SEPARATOR . $file;

        if (is_file($filePath)) {
            $size += filesize($filePath);
        }

        if (is_dir($filePath)) {
            $size += getDirectorySize($filePath);
        }
    }

    return $size;
}

function getFileInfo($path) {
    if (!file_exists($path)) {
        return [
            'error' => 'Путь не существует',
            'fullLocation' => '',
            'name' => '',
            'createdAt' => '',
            'modifiedAt' => '',
            'accessedAt' => '',
            'type' => 'unknown',
            'size' => '',
            'mimeType' => '',
            'extension' => '',
            'permissions' => '',
            'hash' => ''
        ];
    }

    $info = [];

    $info['fullLocation'] = realpath($path) ?: '';
    $info['name'] = basename($path) ?: '';

    $info['createdAt'] = date("d.m.y, H:i", filectime($path)) ?: '';
    $info['modifiedAt'] = date("d.m.y, H:i", filemtime($path)) ?: '';
    $info['accessedAt'] = date("d.m.y, H:i", fileatime($path)) ?: '';

    if (is_dir($path)) {
        $info['type'] = 'directory';
        $size = getDirectorySize($path);
        $info['size'] = formatSize($size);
        $info['permissions'] = substr(sprintf('%o', fileperms($path)), -4) ?: '';
    } elseif (is_file($path)) {
        $info['type'] = 'file';
        $size = filesize($path);
        $info['size'] = formatSize($size);
        $info['mimeType'] = mime_content_type($path) ?: '';
        $info['extension'] = pathinfo($path, PATHINFO_EXTENSION) ?: '';
        $info['permissions'] = substr(sprintf('%o', fileperms($path)), -4) ?: '';
        $info['hash'] = md5_file($path) ?: '';
    } else {
        return [
            'error' => 'Неизвестный тип пути',
            'fullLocation' => '',
            'name' => '',
            'createdAt' => '',
            'modifiedAt' => '',
            'accessedAt' => '',
            'type' => 'unknown',
            'size' => '',
            'mimeType' => '',
            'extension' => '',
            'permissions' => '',
            'hash' => ''
        ];
    }

    return $info;
}

// Получение пути из параметра GET
$dir = isset($_GET['dir']) ? urldecode(base64_decode($_GET['dir'])) : $_SERVER["DOCUMENT_ROOT"];
$path = realpath($dir);

$fileInfo = getFileInfo($path);

// Вывод результата
echo json_encode($fileInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>
