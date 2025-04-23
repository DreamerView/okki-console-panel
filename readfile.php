<?php
// Получаем путь к файлу из параметра GET
$file = isset($_GET['file']) ? urldecode(base64_decode($_GET['file'])) : '';

// Формируем полный путь к файлу
$filePath = $file;

// Проверяем, существует ли файл
if (!file_exists($filePath)) {
    echo 'Файл не найден: ' . htmlspecialchars($filePath);
    exit;
}

// Получаем имя файла
$fileName = basename($filePath);

// Определяем MIME-тип файла
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->file($filePath);

// Функция для проверки, является ли файл текстовым или из application/
function isEditableMimeType($mimeType) {
    return strpos($mimeType, 'text/') === 0 || strpos($mimeType, 'application/') === 0;
}

// Функция для проверки, является ли файл изображением
function isImageMimeType($mimeType) {
    return strpos($mimeType, 'image/') === 0;
}

// Функция для проверки, является ли файл видео
function isVideoMimeType($mimeType) {
    return strpos($mimeType, 'video/') === 0;
}

// Читаем содержимое файла
$content = file_get_contents($filePath);

// Начинаем вывод HTML
if (isEditableMimeType($mimeType)) { ?>
    <textarea class="form-control" rows="20" cols="80"><?=$content;?></textarea>
<?php 
} elseif (isImageMimeType($mimeType)) { ?>
    <div class="d-flex justify-content-center">
        <img style="max-width: 600px;width:100%;opacity:0.7" class="rounded" src="<?=str_replace($_SERVER["DOCUMENT_ROOT"], "", str_replace("\\", "/", $filePath));?>" alt="<?=htmlspecialchars($fileName);?>">
    </div>
<? } elseif (isVideoMimeType($mimeType)) {
    // Для видео (video/*), убираем полный путь
    echo '<video controls style="max-width: 600px; max-height: 400px;">
            <source src="' . str_replace($_SERVER["DOCUMENT_ROOT"], "", str_replace("\\", "/", $filePath)) . '" type="' . htmlspecialchars($mimeType) . '">
            Ваш браузер не поддерживает видео.
          </video>';
} else {
    // Для других типов файлов
    echo '<p>Файл типа "' . htmlspecialchars($mimeType) . '" не поддерживается для предварительного просмотра.</p>';
}
?>
