<?php

class BookRepository {
    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function getAll(): array {
        return $this->pdo->query('SELECT * FROM books ORDER BY id')->fetchAll();
    }

    public function searchByTitle(string $term): array {
        // Екрануємо спецсимволи LIKE (| % _), щоб пошук "50%" шукав саме "50%"
        $escaped = str_replace(['|', '%', '_'], ['||', '|%', '|_'], $term);
        $stmt = $this->pdo->prepare(
            "SELECT * FROM books WHERE title LIKE :pattern ESCAPE '|' ORDER BY id"
        );
        $stmt->execute([':pattern' => '%' . $escaped . '%']);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array {
        $stmt = $this->pdo->prepare('SELECT * FROM books WHERE id = :id');
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row === false ? null : $row;
    }

    public function addBook(string $title, string $author, int $year, string $isbn, bool $isAvailable): int {
        $stmt = $this->pdo->prepare(
            'INSERT INTO books (title, author, year, isbn, is_available)
             VALUES (:title, :author, :year, :isbn, :is_available)'
        );
        $stmt->execute([
            ':title' => $title,
            ':author' => $author,
            ':year' => $year,
            ':isbn' => $isbn,
            ':is_available' => $isAvailable ? 1 : 0,
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}