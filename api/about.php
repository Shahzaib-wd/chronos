<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('GET');

$stmt = db()->prepare('SELECT title, content_html FROM about_page WHERE id=1');
$stmt->execute();
$data = $stmt->fetch() ?: ['title' => 'About Us', 'content_html' => ''];

json_response(['success' => true, 'data' => $data]);
