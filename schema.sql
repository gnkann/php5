CREATE DATABASE IF NOT EXISTS practicum4 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE practicum4;

DROP TABLE IF EXISTS books;

CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    author VARCHAR(255) NOT NULL,
    year INT NOT NULL,
    isbn VARCHAR(13) NOT NULL,
    is_available TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO books (title, author, year, isbn, is_available) VALUES
('Сенна: Життя без меж', 'Том Ратт', 2004, '9781234567897', 1),
('Формула Шумахера', 'Джеймс Аллен', 2007, '9781234567898', 0),
('Гонка століття: Хант проти Лауди', 'Крейг Ллойд', 2015, '9781234567899', 1),
('Хемілтон: Шлях до вершини', 'Меттью Прайор', 2021, '9781234567900', 1);