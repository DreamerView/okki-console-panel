<?php
// Параметры подключения к MySQL
$servername = "127.127.126.26";
$username = "root";
$oldPassword = ""; // Старый пароль
$newPassword = "M4raumIp"; // Новый пароль

try {
    // Подключаемся к MySQL
    $pdo = new PDO("mysql:host=$servername", $username, $oldPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Выполняем команду ALTER USER
    $sql = "ALTER USER 'root'@'localhost' IDENTIFIED BY :newPassword";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
    $stmt->execute();

    echo "Пароль успешно изменен.";
} catch (PDOException $e) {
    echo "Ошибка: " . $e->getMessage();
}

// Закрываем соединение
$pdo = null;
?>