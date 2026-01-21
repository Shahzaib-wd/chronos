<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$msg = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $msg = 'CSRF validation failed.';
    } else {
        $id = (int)($_POST['id'] ?? 0);
        $status = (string)($_POST['status'] ?? 'pending');
        $allowed = ['pending','confirmed','shipped','delivered','cancelled'];
        if ($id > 0 && in_array($status, $allowed, true)) {
            $pdo->prepare('UPDATE orders SET status=? WHERE id=?')->execute([$status, $id]);
            $msg = 'Order updated.';
        }
    }
}

$orders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC')->fetchAll();
?>
<h2 class="mb-3">Orders</h2>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>Date</th><th>Order ID</th><th>Customer</th><th>Phone</th><th>Total</th><th>Status</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td class="small text-white-50"><?= e($o['created_at']) ?></td>
            <td><?= e($o['order_uid']) ?></td>
            <td><?= e($o['full_name']) ?></td>
            <td><?= e($o['phone']) ?></td>
            <td>Rs. <?= e(number_format((float)$o['total_amount'],0,'.',',')) ?></td>
            <td>
              <form method="post" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
                <select name="status" class="form-select form-select-sm" style="max-width:160px">
                  <?php foreach (['pending','confirmed','shipped','delivered','cancelled'] as $s): ?>
                    <option value="<?= e($s) ?>" <?= $o['status']===$s?'selected':'' ?>><?= e(ucfirst($s)) ?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-warning">Save</button>
              </form>
            </td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-warning" href="view_order.php?id=<?= (int)$o['id'] ?>">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
