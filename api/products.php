<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('GET');

$brand = trim((string)($_GET['brand'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));
$min = isset($_GET['min']) ? (float)$_GET['min'] : null;
$max = isset($_GET['max']) ? (float)$_GET['max'] : null;
$q = trim((string)($_GET['q'] ?? ''));
$featured = isset($_GET['featured']) ? (int)$_GET['featured'] : null;

$where = ['p.status = 1'];
$params = [];

if ($brand !== '') {
    $where[] = 'b.slug = ?';
    $params[] = $brand;
}
if ($category !== '') {
    $where[] = 'c.slug = ?';
    $params[] = $category;
}
if ($min !== null) {
    $where[] = 'COALESCE(p.discount_price, p.price) >= ?';
    $params[] = $min;
}
if ($max !== null) {
    $where[] = 'COALESCE(p.discount_price, p.price) <= ?';
    $params[] = $max;
}
if ($q !== '') {
    $where[] = '(p.name LIKE ? OR p.sku LIKE ? OR b.name LIKE ?)';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
    $params[] = '%' . $q . '%';
}
if ($featured !== null) {
    $where[] = 'p.is_featured = ?';
    $params[] = $featured;
}

$sql = 'SELECT p.id, p.name, p.sku, p.description_html, p.price, p.discount_price, p.stock_qty, p.is_featured,
               p.status, b.name AS brand_name, b.slug AS brand_slug, c.name AS category_name, c.slug AS category_slug,
               (SELECT image_path FROM product_images pi WHERE pi.product_id = p.id ORDER BY pi.sort_order ASC, pi.id ASC LIMIT 1) AS image_path
        FROM products p
        JOIN brands b ON b.id = p.brand_id
        JOIN categories c ON c.id = p.category_id
        WHERE ' . implode(' AND ', $where) . '
        ORDER BY p.created_at DESC, p.id DESC';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$rows = $stmt->fetchAll();

$out = [];
foreach ($rows as $r) {
    $out[] = [
        'id' => (string)$r['id'],
        'brand' => $r['brand_name'],
        'brand_slug' => $r['brand_slug'],
        'category' => $r['category_name'],
        'category_slug' => $r['category_slug'],
        'model' => $r['name'],
        'name' => $r['name'],
        'sku' => $r['sku'],
        'price' => (float)($r['discount_price'] ?? $r['price']),
        'originalPrice' => $r['discount_price'] !== null ? (float)$r['price'] : null,
        'stock' => (int)$r['stock_qty'],
        'image' => $r['image_path'] ? ('admin/assets/uploads/products/' . $r['image_path']) : null,
        'isNew' => false,
        'isFeatured' => (bool)$r['is_featured'],
    ];
}

json_response(['success' => true, 'data' => $out]);
