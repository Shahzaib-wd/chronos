<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$msg = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $msg = 'CSRF validation failed.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            $name = trim((string)($_POST['name'] ?? ''));
            $slug = trim((string)($_POST['slug'] ?? ''));
            $status = isset($_POST['status']) ? 1 : 0;
            if ($name !== '' && $slug !== '') {
                $stmt = $pdo->prepare('INSERT INTO categories (name, slug, status) VALUES (?, ?, ?)');
                $stmt->execute([$name, $slug, $status]);
                $msg = 'Category created.';
            }
        }
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('DELETE FROM categories WHERE id=?')->execute([$id]);
            $msg = 'Category deleted.';
        }
    }
}

$cats = $pdo->query('SELECT * FROM categories ORDER BY id DESC')->fetchAll();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h2 class="mb-0">Categories</h2>
</div>

<?php if ($msg): ?>
  <div class="alert alert-info"><?= e($msg) ?></div>
<?php endif; ?>

<div class="card p-3 mb-3">
  <form method="post" class="row g-2 align-items-end">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-12 col-md-4">
      <label class="form-label">Name</label>
      <input name="name" class="form-control" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Slug</label>
      <input name="slug" class="form-control" required>
    </div>
    <div class="col-12 col-md-2">
      <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" name="status" checked>
        <label class="form-check-label">Active</label>
      </div>
    </div>
    <div class="col-12 col-md-2">
      <button class="btn btn-warning w-100" type="submit">Add</button>
    </div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>Slug</th><th>Status</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($cats as $c): ?>
          <tr>
            <td><?= (int)$c['id'] ?></td>
            <td><?= e($c['name']) ?></td>
            <td><?= e($c['slug']) ?></td>
            <td><?= $c['status'] ? 'Active' : 'Inactive' ?></td>
            <td class="text-end">
              <form method="post" onsubmit="return confirm('Delete category?');" style="display:inline">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                <button class="btn btn-sm btn-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
