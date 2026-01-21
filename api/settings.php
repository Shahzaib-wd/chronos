<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('GET');

$stmt = db()->prepare('SELECT `key`, `value` FROM settings');
$stmt->execute();
$rows = $stmt->fetchAll();
$settings = [];
foreach ($rows as $r) {
    $settings[$r['key']] = $r['value'];
}

json_response(['success' => true, 'data' => $settings]);
