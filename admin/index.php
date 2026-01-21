<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$stats = [];
$stats['products'] = (int)$pdo->query('SELECT COUNT(*) c FROM products')->fetch()['c'];
$stats['orders'] = (int)$pdo->query('SELECT COUNT(*) c FROM orders')->fetch()['c'];
$stats['pending_orders'] = (int)$pdo->query("SELECT COUNT(*) c FROM orders WHERE status='pending'")->fetch()['c'];
$stats['messages'] = (int)$pdo->query('SELECT COUNT(*) c FROM contact_messages')->fetch()['c'];
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h2 class="mb-1">Dashboard</h2>
    <div class="text-white-50 small">Welcome, <?= e((string)($_SESSION['admin_name'] ?? 'Admin')) ?></div>
  </div>
</div>

<div class="row g-3">
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card p-3">
      <div class="text-white-50">Products</div>
      <div class="h3 mb-0"><?= $stats['products'] ?></div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card p-3">
      <div class="text-white-50">Orders</div>
      <div class="h3 mb-0"><?= $stats['orders'] ?></div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card p-3">
      <div class="text-white-50">Pending</div>
      <div class="h3 mb-0"><?= $stats['pending_orders'] ?></div>
    </div>
  </div>
  <div class="col-12 col-md-6 col-lg-3">
    <div class="card p-3">
      <div class="text-white-50">Contact Messages</div>
      <div class="h3 mb-0"><?= $stats['messages'] ?></div>
    </div>
  </div>
</div>

<div class="mt-4 card p-3">
  <div class="d-flex justify-content-between align-items-center">
    <div>
      <div class="text-white-50">Quick Actions</div>
      <div class="small">Manage products, orders, and homepage content.</div>
    </div>
    <div class="d-flex gap-2">
      <a class="btn btn-outline-warning" href="products.php">Products</a>
      <a class="btn btn-outline-warning" href="orders.php">Orders</a>
      <a class="btn btn-outline-warning" href="homepage_sections.php">Homepage</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
