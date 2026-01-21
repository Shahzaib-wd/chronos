<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('GET');

$stmt = db()->prepare('SELECT id, title, subtitle, content_html, image_path, position, status FROM homepage_sections WHERE status = 1 ORDER BY position ASC, id ASC');
$stmt->execute();
$sections = $stmt->fetchAll();

// Make absolute-ish paths for frontend
foreach ($sections as &$s) {
    if (!empty($s['image_path'])) {
        $s['image_url'] = 'admin/assets/uploads/sections/' . $s['image_path'];
    } else {
        $s['image_url'] = null;
    }
}

json_response(['success' => true, 'data' => $sections]);
