<?php
error_reporting(E_ALL);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/classes/BookRepository.php';
require_once __DIR__ . '/lib/validation.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    echo json_encode(['error' => 'Дозволено лише POST-запити'], JSON_UNESCAPED_UNICODE);
    exit;
}

[$data, $errors] = validateBookInput($_POST);

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['errors' => $errors], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $repo = new BookRepository($pdo);
    $newId = $repo->addBook(
        $data['title'],
        $data['author'],
        (int) $data['year'],
        $data['isbn'],
        $data['is_available']
    );
    $book = $repo->findById($newId); // повертаємо запис саме так, як він збережений у базі

    http_response_code(201);
    echo json_encode($book, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Помилка бази даних'], JSON_UNESCAPED_UNICODE);
}