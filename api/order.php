<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('GET');

$order_uid = trim((string)($_GET['order_uid'] ?? ''));
if ($order_uid === '') {
    json_response(['success' => false, 'message' => 'Missing order id'], 422);
}

$stmt = db()->prepare('SELECT * FROM orders WHERE order_uid = ?');
$stmt->execute([$order_uid]);
$order = $stmt->fetch();
if (!$order) {
    json_response(['success' => false, 'message' => 'Order not found'], 404);
}

$itemStmt = db()->prepare('SELECT oi.quantity, oi.price_at_purchase, p.name, p.sku
                           FROM order_items oi
                           JOIN products p ON p.id = oi.product_id
                           WHERE oi.order_id = ?');
$itemStmt->execute([(int)$order['id']]);
$items = $itemStmt->fetchAll();

json_response(['success' => true, 'data' => ['order' => $order, 'items' => $items]]);
