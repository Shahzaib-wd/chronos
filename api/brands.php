<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('GET');
$stmt = db()->query('SELECT id, name, slug FROM brands WHERE status=1 ORDER BY name ASC');
json_response(['success' => true, 'data' => $stmt->fetchAll()]);
