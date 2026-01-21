<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$msg = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $msg = 'CSRF validation failed.';
    } else {
        $pairs = [
            'store_name' => trim((string)($_POST['store_name'] ?? '')),
            'currency' => trim((string)($_POST['currency'] ?? 'PKR')),
            'support_phone' => trim((string)($_POST['support_phone'] ?? '')),
            'support_email' => trim((string)($_POST['support_email'] ?? '')),
            'store_address' => trim((string)($_POST['store_address'] ?? '')),
        ];
        $stmt = $pdo->prepare('INSERT INTO settings (`key`,`value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`)');
        foreach ($pairs as $k => $v) {
            $stmt->execute([$k, $v]);
        }
        $msg = 'Settings saved.';
    }
}

$rows = $pdo->query('SELECT `key`,`value` FROM settings')->fetchAll();
$settings = [];
foreach ($rows as $r) $settings[$r['key']] = $r['value'];

function val($k, $default=''){
  global $settings;
  return $settings[$k] ?? $default;
}
?>
<h2 class="mb-3">Store Settings</h2>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">Store Name</label>
        <input class="form-control" name="store_name" value="<?= e((string)val('store_name','CHRONOS')) ?>">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Currency</label>
        <input class="form-control" name="currency" value="<?= e((string)val('currency','PKR')) ?>" readonly>
        <div class="small text-white-50 mt-1">Prices are displayed in PKR (Rs.)</div>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Support Phone</label>
        <input class="form-control" name="support_phone" value="<?= e((string)val('support_phone','')) ?>">
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Support Email</label>
        <input class="form-control" name="support_email" value="<?= e((string)val('support_email','')) ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Store Address</label>
        <textarea class="form-control" name="store_address" rows="3"><?= e((string)val('store_address','')) ?></textarea>
      </div>
    </div>
    <div class="mt-3">
      <button class="btn btn-warning">Save</button>
    </div>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
