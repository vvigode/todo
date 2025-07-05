<?php
// router.php – упрощённый роутер для built-in PHP web-server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Если запрашивают существующий файл — отдаём его как есть
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Иначе — всё обрабатывает Yii через index.php
require_once __DIR__ . '/index.php'; 