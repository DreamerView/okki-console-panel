<?php
// api/execute_sql.php

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $command = $_POST['command'];

    // Подключение к базе данных MySQL
    $mysqli = new mysqli("127.127.126.26", "root", "");

    if ($mysqli->connect_error) {
        echo json_encode(['success' => false, 'error' => 'Ошибка соединения с базой данных']);
        exit;
    }

    // Выполнение SQL команды
    if ($result = $mysqli->query($command)) {
        if ($result === true) {
            echo json_encode(['success' => true, 'data' => 'Команда выполнена успешно']);
        } else {
            // Для SELECT запросов возвращаем результат в виде таблицы
            $data = [];
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
            echo json_encode(['success' => true, 'data' => json_encode($data)]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => $mysqli->error]);
    }

    $mysqli->close();
}
