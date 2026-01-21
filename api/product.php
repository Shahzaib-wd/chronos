<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('GET');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    json_response(['success' => false, 'message' => 'Invalid product id'], 422);
}

$stmt = db()->prepare('SELECT p.*, b.name AS brand_name, c.name AS category_name
                       FROM products p
                       JOIN brands b ON b.id = p.brand_id
                       JOIN categories c ON c.id = p.category_id
                       WHERE p.id = ? AND p.status = 1');
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) {
    json_response(['success' => false, 'message' => 'Product not found'], 404);
}

$imgStmt = db()->prepare('SELECT image_path FROM product_images WHERE product_id = ? ORDER BY sort_order ASC, id ASC');
$imgStmt->execute([$id]);
$imgs = $imgStmt->fetchAll();

$images = [];
foreach ($imgs as $im) {
    $images[] = 'admin/assets/uploads/products/' . $im['image_path'];
}

$data = [
    'id' => (string)$p['id'],
    'brand' => $p['brand_name'],
    'category' => $p['category_name'],
    'name' => $p['name'],
    'model' => $p['name'],
    'sku' => $p['sku'],
    'description_html' => $p['description_html'],
    'price' => (float)($p['discount_price'] ?? $p['price']),
    'originalPrice' => $p['discount_price'] !== null ? (float)$p['price'] : null,
    'stock' => (int)$p['stock_qty'],
    'images' => $images,
];

json_response(['success' => true, 'data' => $data]);
