<?php
error_reporting(E_ALL);
ini_set('display_errors', '0'); // щоб помилки PHP не ламали JSON

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/classes/BookRepository.php';

$repo = new BookRepository($pdo);

$q = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';

try {
    $rows = $q === '' ? $repo->getAll() : $repo->searchByTitle($q);
    echo json_encode($rows, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Помилка бази даних'], JSON_UNESCAPED_UNICODE);
}