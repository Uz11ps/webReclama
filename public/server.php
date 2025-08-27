<?php
// Маршрутизатор для встроенного PHP-сервера
// Если запрошен реальный файл (CSS/JS/изображение) — отдать его как статику
$uriPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$file = realpath(__DIR__ . $uriPath);
if ($uriPath !== '/' && $file !== false && str_starts_with($file, realpath(__DIR__)) && is_file($file)) {
    return false; // Пусть встроенный сервер отдаст файл напрямую
}

// Иначе — рендерим приложение
require __DIR__ . '/index.php';

