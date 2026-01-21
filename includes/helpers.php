<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function json_response(array $data, int $status = 200): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

function require_method(string $method): void {
    $m = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    if ($m !== strtoupper($method)) {
        json_response(['success' => false, 'message' => 'Method Not Allowed'], 405);
    }
}

function get_json_input(): array {
    $raw = file_get_contents('php://input');
    if (!$raw) return [];
    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function currency_symbol(): string {
    return 'Rs.';
}

function format_pkr(int|float $amount): string {
    // Keep UI identical: most templates already render "Rs. " + number with commas.
    // This helper is used for backend-generated values.
    return number_format((float)$amount, 0, '.', ',');
}
