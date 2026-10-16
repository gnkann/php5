<?php

$dbHost = '127.0.0.1';
$dbName = 'practicum4';
$dbUser = 'root';
$dbPass = 'orange33';

try {
    $pdo = new PDO(
        "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('Не вдалося підключитися до бази даних. Перевірте налаштування в db.php.');
}

return $pdo;