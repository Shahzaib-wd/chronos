<?php
// Dev router for PHP built-in server (optional)
// Run: php -S localhost:8000 router.php

declare(strict_types=1);

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$file = __DIR__ . $path;

if ($path !== '/' && is_file($file)) {
    return false;
}

// Default document
if ($path === '/' || $path === '/index.php') {
    include __DIR__ . '/index.html';
    exit;
}

// Allow .html direct
if (str_ends_with($path, '.html') && is_file(__DIR__ . $path)) {
    include __DIR__ . $path;
    exit;
}

http_response_code(404);
echo 'Not Found';
