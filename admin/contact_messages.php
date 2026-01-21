<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$rows = db()->query('SELECT * FROM contact_messages ORDER BY created_at DESC')->fetchAll();
?>
<h2 class="mb-3">Contact Messages</h2>
<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>Date</th><th>Name</th><th>Phone</th><th>Email</th><th>Message</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $m): ?>
          <tr>
            <td class="small text-white-50"><?= e($m['created_at']) ?></td>
            <td><?= e($m['name']) ?></td>
            <td><?= e($m['phone']) ?></td>
            <td><?= e((string)$m['email']) ?></td>
            <td style="max-width:520px;white-space:pre-wrap"><?= e($m['message']) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
