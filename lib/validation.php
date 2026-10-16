<?php

/**
 * Перевіряє дані форми книги.
 * Повертає [очищені дані, масив помилок].
 */
function validateBookInput(array $input): array {
    $get = fn(string $key): string => is_string($input[$key] ?? null) ? trim($input[$key]) : '';

    $data = [
        'title'        => $get('title'),
        'author'       => $get('author'),
        'year'         => $get('year'),
        'isbn'         => $get('isbn'),
        'is_available' => isset($input['is_available']),
    ];

    $errors = [];
    $currentYear = (int) date('Y');

    if ($data['title'] === '') {
        $errors['title'] = 'Назва обов\'язкова';
    }
    if ($data['author'] === '') {
        $errors['author'] = 'Автор обов\'язковий';
    }
    if (!ctype_digit($data['year']) || (int) $data['year'] < 1450 || (int) $data['year'] > $currentYear) {
        $errors['year'] = "Рік має бути числом від 1450 до $currentYear";
    }
    if (!preg_match('/^\d{10}(\d{3})?$/', $data['isbn'])) {
        $errors['isbn'] = 'ISBN має містити 10 або 13 цифр';
    }

    return [$data, $errors];
}