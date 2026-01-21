<?php
declare(strict_types=1);
require_once __DIR__ . '/_bootstrap.php';

require_method('POST');

$input = get_json_input();
$customer = $input['customer'] ?? [];
$items = $input['items'] ?? [];

$full_name = trim((string)($customer['full_name'] ?? ''));
$phone = trim((string)($customer['phone'] ?? ''));
$email = trim((string)($customer['email'] ?? ''));
$address = trim((string)($customer['address'] ?? ''));
$city = trim((string)($customer['city'] ?? ''));
$notes = trim((string)($customer['notes'] ?? ''));
$payment_method = 'COD';

$errors = [];
if ($full_name === '' || mb_strlen($full_name) > 120) $errors[] = 'Full name is required';
if ($phone === '' || mb_strlen($phone) > 40) $errors[] = 'Phone is required';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email';
if ($address === '' || mb_strlen($address) > 255) $errors[] = 'Address is required';
if ($city === '' || mb_strlen($city) > 120) $errors[] = 'City is required';
if (!is_array($items) || count($items) < 1) $errors[] = 'Cart is empty';

if ($errors) {
    json_response(['success' => false, 'message' => implode(', ', $errors)], 422);
}

// Normalize items: expect [{product_id, qty}] OR [{id, quantity}] from localStorage data
$norm = [];
foreach ($items as $it) {
    $pid = (int)($it['product_id'] ?? $it['id'] ?? 0);
    $qty = (int)($it['qty'] ?? $it['quantity'] ?? 1);
    if ($pid > 0 && $qty > 0) {
        $norm[] = ['product_id' => $pid, 'qty' => $qty];
    }
}
if (!$norm) {
    json_response(['success' => false, 'message' => 'Invalid cart items'], 422);
}

$pdo = db();
$pdo->beginTransaction();
try {
    // Lock products to prevent race conditions
    $total = 0.0;
    $lineItems = [];

    foreach ($norm as $n) {
        $stmt = $pdo->prepare('SELECT id, name, price, discount_price, stock_qty FROM products WHERE id=? AND status=1 FOR UPDATE');
        $stmt->execute([$n['product_id']]);
        $p = $stmt->fetch();
        if (!$p) throw new Exception('One of the products is unavailable');
        if ((int)$p['stock_qty'] < $n['qty']) throw new Exception('Insufficient stock for ' . $p['name']);

        $unit = (float)($p['discount_price'] ?? $p['price']);
        $lineTotal = $unit * $n['qty'];
        $total += $lineTotal;

        $lineItems[] = [
            'product_id' => (int)$p['id'],
            'qty' => $n['qty'],
            'unit_price' => $unit,
        ];
    }

    $order_uid = 'CH-' . strtoupper(bin2hex(random_bytes(4))) . '-' . date('ymd');

    $stmt = $pdo->prepare('INSERT INTO orders (order_uid, full_name, phone, email, address, city, notes, payment_method, total_amount, status, created_at)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
    $stmt->execute([$order_uid, $full_name, $phone, $email, $address, $city, $notes, $payment_method, $total, 'pending']);
    $order_id = (int)$pdo->lastInsertId();

    $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase) VALUES (?, ?, ?, ?)');
    $stockStmt = $pdo->prepare('UPDATE products SET stock_qty = stock_qty - ? WHERE id=?');

    foreach ($lineItems as $li) {
        $itemStmt->execute([$order_id, $li['product_id'], $li['qty'], $li['unit_price']]);
        $stockStmt->execute([$li['qty'], $li['product_id']]);
    }

    $pdo->commit();

    json_response([
        'success' => true,
        'data' => [
            'order_id' => $order_id,
            'order_uid' => $order_uid,
            'total' => $total,
            'currency' => 'PKR',
        ]
    ]);
} catch (Throwable $e) {
    $pdo->rollBack();
    json_response(['success' => false, 'message' => $e->getMessage()], 400);
}
