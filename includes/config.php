<?php
// Basic configuration
// NOTE: For production, move secrets to environment variables.

declare(strict_types=1);

// App
if (!defined('APP_NAME')) define('APP_NAME', 'CHRONOS');
if (!defined('APP_BASE_URL')) {
    // Auto-detect base URL (works on localhost/dev). You can override in settings table later.
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = rtrim(str_replace(basename($_SERVER['SCRIPT_NAME'] ?? ''), '', $_SERVER['SCRIPT_NAME'] ?? ''), '/');
    define('APP_BASE_URL', $scheme . '://' . $host . $path);
}

// DB
if (!defined('DB_HOST')) define('DB_HOST', '127.0.0.1');
if (!defined('DB_NAME')) define('DB_NAME', 'chronos_store');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// Uploads
if (!defined('UPLOADS_DIR')) define('UPLOADS_DIR', __DIR__ . '/../admin/assets/uploads');
if (!defined('PRODUCT_UPLOAD_DIR')) define('PRODUCT_UPLOAD_DIR', UPLOADS_DIR . '/products');
if (!defined('SECTION_UPLOAD_DIR')) define('SECTION_UPLOAD_DIR', UPLOADS_DIR . '/sections');

// Security
if (!defined('CSRF_TOKEN_KEY')) define('CSRF_TOKEN_KEY', 'chronos_csrf_token');
