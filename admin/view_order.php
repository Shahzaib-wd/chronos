<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$id = (int)($_GET['id'] ?? 0);
$pdo = db();

$stmt = $pdo->prepare('SELECT * FROM orders WHERE id=?');
$stmt->execute([$id]);
$order = $stmt->fetch();
if (!$order) {
    echo '<div class="alert alert-danger">Order not found.</div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

$itemStmt = $pdo->prepare('SELECT oi.*, p.name, p.sku FROM order_items oi JOIN products p ON p.id=oi.product_id WHERE oi.order_id=?');
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">Order <?= e($order['order_uid']) ?></h2>
  <a class="btn btn-outline-warning" href="orders.php">Back</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-5">
    <div class="card p-3">
      <div class="text-white-50 mb-2">Customer</div>
      <div><strong><?= e($order['full_name']) ?></strong></div>
      <div class="small text-white-50">Phone: <?= e($order['phone']) ?></div>
      <div class="small text-white-50">Email: <?= e((string)$order['email']) ?></div>
      <hr style="border-color:rgba(255,255,255,.12)">
      <div class="text-white-50">Address</div>
      <div><?= e($order['address']) ?></div>
      <div class="small text-white-50">City: <?= e($order['city']) ?></div>
      <div class="small text-white-50">Notes: <?= e((string)$order['notes']) ?></div>
      <hr style="border-color:rgba(255,255,255,.12)">
      <div class="text-white-50">Order</div>
      <div class="small text-white-50">Date: <?= e($order['created_at']) ?></div>
      <div class="small text-white-50">Payment: <?= e($order['payment_method']) ?></div>
      <div class="h5 mt-2">Total: Rs. <?= e(number_format((float)$order['total_amount'],0,'.',',')) ?></div>
    </div>
  </div>
  <div class="col-12 col-lg-7">
    <div class="card p-3">
      <div class="text-white-50 mb-2">Items</div>
      <div class="table-responsive">
        <table class="table table-dark table-striped align-middle mb-0">
          <thead>
            <tr><th>SKU</th><th>Name</th><th>Qty</th><th>Unit</th><th>Total</th></tr>
          </thead>
          <tbody>
            <?php foreach ($items as $it): ?>
              <tr>
                <td><?= e($it['sku']) ?></td>
                <td><?= e($it['name']) ?></td>
                <td><?= (int)$it['quantity'] ?></td>
                <td>Rs. <?= e(number_format((float)$it['price_at_purchase'],0,'.',',')) ?></td>
                <td>Rs. <?= e(number_format(((float)$it['price_at_purchase']*(int)$it['quantity']),0,'.',',')) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
