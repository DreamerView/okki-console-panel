<?php

// Форматирует размер в человеко-читаемый вид
function formatSize($bytes) {
    if ($bytes <= 0) return "0 B";
    $sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $factor = floor(log($bytes, 1024));
    return sprintf("%.2f", $bytes / pow(1024, $factor)) . " " . $sizes[$factor];
}

// Рекурсивно сканирует директорию и возвращает массив файлов и папок
function scanDirectory($dir) {
    $result = [];

    // Добавляем "назад" если это не корень
    if ($dir !== $_SERVER["DOCUMENT_ROOT"]) {
        $parentDir = dirname($dir);
        $result[] = [
            'type' => 'folder',
            'fullLocation' => base64_encode(urlencode(realpath($parentDir))),
            'name' => '...',
            'lastModified' => '',
            'size' => ''
        ];
    }

    // Получаем содержимое директории
    $files = scandir($dir);

    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
        $lastModified = date("d.m.y, H:i", filemtime($fullPath));

        // Если это папка
        if (is_dir($fullPath)) {
            $folderData = [
                'type' => 'folder',
                'fullLocation' => base64_encode(urlencode(realpath($fullPath))),
                'name' => $file,
                'lastModified' => $lastModified,
                'size' => 0
            ];

            // Рекурсивно считаем размер содержимого
            $subfolderResult = scanDirectory($fullPath);
            foreach ($subfolderResult as $item) {
                $folderData['size'] += isset($item['size']) && is_numeric($item['size']) ? (int)$item['size'] : 0;
            }

            $folderData['size'] = formatSize($folderData['size']);
            $result[] = $folderData;

        } else {
            // Файл
            $size = filesize($fullPath);
            $result[] = [
                'type' => 'file',
                'fullLocation' => base64_encode(urlencode(realpath($fullPath))),
                'name' => $file,
                'lastModified' => $lastModified,
                'size' => formatSize($size)
            ];
        }
    }

    // Сортировка: папки сначала
    usort($result, function($a, $b) {
        if ($a['type'] === 'folder' && $b['type'] === 'file') return -1;
        if ($a['type'] === 'file' && $b['type'] === 'folder') return 1;
        return strcmp($a['name'], $b['name']); // сортировка по имени внутри
    });

    return $result;
}

// Получаем директорию из GET или берём DOCUMENT_ROOT
$currentDirectory = isset($_GET['dir']) ? urldecode(base64_decode($_GET['dir'])) : $_SERVER["DOCUMENT_ROOT"];
$directoryResult = scanDirectory($currentDirectory);
echo json_encode($directoryResult, JSON_UNESCAPED_UNICODE);
