<?php
// Массив для хранения сообщений в памяти
$messages = [];

// Проверяем, пришел ли запрос от Telegram
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получаем данные от Telegram
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);

    // Проверяем, пришло ли сообщение
    if (isset($update['message'])) {
        $message = $update['message']['text'];

        // Добавляем сообщение в массив
        $messages[] = $message;

        // Уведомляем клиента через SSE
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');

        // Отправляем сообщение клиенту
        echo "data: {$message}\n\n";
        ob_flush();
        flush();
    }
} else {
    // Обработка запросов от клиента (SSE)
    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');

    // Отправляем все сообщения клиенту
    if (!empty($messages)) {
        foreach ($messages as $message) {
            echo "data: {$message}\n\n";
            ob_flush();
            flush();
        }
    } else {
        // Если нет новых сообщений, отправляем пустое сообщение
        echo "data: \n\n";
    }
}
