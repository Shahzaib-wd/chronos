<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$msg = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $msg = 'CSRF validation failed.';
    } else {
        $title = trim((string)($_POST['title'] ?? 'About')); 
        $content = (string)($_POST['content_html'] ?? '');
        $stmt = $pdo->prepare('INSERT INTO about_page (id, title, content_html, updated_at) VALUES (1, ?, ?, NOW()) ON DUPLICATE KEY UPDATE title=VALUES(title), content_html=VALUES(content_html), updated_at=NOW()');
        $stmt->execute([$title, $content]);
        $msg = 'About page updated.';
    }
}

$row = $pdo->query('SELECT title, content_html FROM about_page WHERE id=1')->fetch();
$title = $row['title'] ?? 'About CHRONOS';
$content = $row['content_html'] ?? '';
?>
<h2 class="mb-3">About Page</h2>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>
<div class="card p-3">
  <form method="post">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="mb-3">
      <label class="form-label">Title</label>
      <input class="form-control" name="title" value="<?= e($title) ?>" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Content (HTML allowed)</label>
      <textarea class="form-control" name="content_html" rows="10"><?= e($content) ?></textarea>
      <div class="small text-white-50 mt-1">Tip: You can paste rich HTML. It will be rendered on About page.</div>
    </div>
    <button class="btn btn-warning">Save</button>
  </form>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
