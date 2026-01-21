<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/helpers.php';

session_name('chronos_admin');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function admin_logged_in(): bool {
    return !empty($_SESSION['admin_id']);
}

function require_admin(): void {
    if (!admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function csrf_token(): string {
    if (empty($_SESSION[CSRF_TOKEN_KEY])) {
        $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_KEY];
}

function csrf_validate(?string $token): bool {
    if (!$token || empty($_SESSION[CSRF_TOKEN_KEY])) return false;
    return hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
}

function admin_logout(): void {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'], $params['secure'], $params['httponly']
        );
    }
    session_destroy();
}
