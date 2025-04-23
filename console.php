<?php
function formatSize($bytes) {
    $sizes = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.2f", $bytes / pow(1024, $factor)) . " " . $sizes[$factor];
}

$path = $_SERVER['DOCUMENT_ROOT']; // Укажите путь к нужному диску

// Общее пространство на диске
$totalSpace = disk_total_space($path);

// Свободное пространство на диске
$freeSpace = disk_free_space($path);

// Занятое пространство на диске
$usedSpace = $totalSpace - $freeSpace;
$usedPercentage = ($usedSpace / $totalSpace) * 100;

function scanDirectory($dir) {
    $result = [];

    // Открываем директорию
    $files = scandir($dir);

    foreach ($files as $file) {
        // Пропускаем '.' и '..'
        if ($file === '.' || $file === '..') {
            continue;
        }

        $fullPath = $dir . DIRECTORY_SEPARATOR . $file;
        $lastModified = date("d.m.y, H:i", filemtime($fullPath)); // Получаем время последнего изменения

        // Определяем размер файла (если это не директория)
        $size = is_file($fullPath) ? filesize($fullPath) : 0;

        // Определяем, файл это или папка
        if (is_dir($fullPath)) {
            $result[] = [
                'type' => 'folder',
                'fullLocation' => realpath($fullPath),
                'name' => $file,
                'lastModified' => $lastModified,
                'size' => ""
            ];

            // Рекурсивно сканируем вложенные папки
            $result = array_merge($result, scanDirectory($fullPath));
        } else {
            $result[] = [
                'type' => 'file',
                'fullLocation' => realpath($fullPath),
                'name' => $file,
                'lastModified' => $lastModified,
                'size' => formatSize($size)
            ];
        }
    }

    return $result;
}

// Пример использования
$directory = $_SERVER["DOCUMENT_ROOT"]; // Укажите путь к директории
$directoryResult = scanDirectory($directory);

$response = Array(
    "fullPath"=>$path,
    "totalSpace"=>formatSize($totalSpace),
    "freeSpace"=>formatSize($freeSpace),
    "usedSpace"=>formatSize($usedSpace),
    "usedPercentage"=>$usedPercentage,
    "extension"=>get_loaded_extensions(),
    "fileFolder"=>$directoryResult
);
echo json_encode($response);
?>