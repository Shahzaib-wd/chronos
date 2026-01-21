<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$msg = '';

function safe_upload_image(string $field, string $destDir, array $allowed = ['image/jpeg','image/png','image/webp']): ?string {
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) return null;
    $tmp = $_FILES[$field]['tmp_name'];
    $mime = mime_content_type($tmp) ?: '';
    if (!in_array($mime, $allowed, true)) return null;
    $ext = match($mime) {
        'image/png' => 'png',
        'image/webp' => 'webp',
        default => 'jpg'
    };
    if (!is_dir($destDir)) mkdir($destDir, 0775, true);
    $name = bin2hex(random_bytes(8)) . '.' . $ext;
    $path = rtrim($destDir,'/') . '/' . $name;
    if (!move_uploaded_file($tmp, $path)) return null;
    return $name;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $msg = 'CSRF validation failed.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            $title = trim((string)($_POST['title'] ?? ''));
            $subtitle = trim((string)($_POST['subtitle'] ?? ''));
            $content = (string)($_POST['content_html'] ?? '');
            $position = (int)($_POST['position'] ?? 0);
            $status = isset($_POST['status']) ? 1 : 0;
            $image = safe_upload_image('image', SECTION_UPLOAD_DIR);
            $stmt = $pdo->prepare('INSERT INTO homepage_sections (title, subtitle, content_html, image_path, position, status) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$title, $subtitle, $content, $image, $position, $status]);
            $msg = 'Section created.';
        }
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            $row = $pdo->prepare('SELECT image_path FROM homepage_sections WHERE id=?');
            $row->execute([$id]);
            $r = $row->fetch();
            if ($r && $r['image_path']) {
                $fp = SECTION_UPLOAD_DIR . '/' . $r['image_path'];
                if (is_file($fp)) @unlink($fp);
            }
            $pdo->prepare('DELETE FROM homepage_sections WHERE id=?')->execute([$id]);
            $msg = 'Section deleted.';
        }
        if ($action === 'toggle') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('UPDATE homepage_sections SET status = 1 - status WHERE id=?')->execute([$id]);
            $msg = 'Section updated.';
        }
    }
}

$rows = $pdo->query('SELECT * FROM homepage_sections ORDER BY position ASC, id DESC')->fetchAll();
?>
<h2 class="mb-3">Homepage Sections</h2>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card p-3 mb-3">
  <div class="text-white-50 mb-2">Add Section</div>
  <form method="post" enctype="multipart/form-data" class="row g-2">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">
    <div class="col-12 col-md-4">
      <label class="form-label">Title</label>
      <input class="form-control" name="title" required>
    </div>
    <div class="col-12 col-md-4">
      <label class="form-label">Subtitle</label>
      <input class="form-control" name="subtitle">
    </div>
    <div class="col-12 col-md-2">
      <label class="form-label">Position</label>
      <input class="form-control" type="number" name="position" value="10">
    </div>
    <div class="col-12 col-md-2">
      <div class="form-check mt-4">
        <input class="form-check-input" type="checkbox" name="status" checked>
        <label class="form-check-label">Active</label>
      </div>
    </div>
    <div class="col-12">
      <label class="form-label">Content (HTML allowed)</label>
      <textarea class="form-control" name="content_html" rows="3"></textarea>
    </div>
    <div class="col-12 col-md-6">
      <label class="form-label">Image (optional)</label>
      <input class="form-control" type="file" name="image" accept="image/*">
    </div>
    <div class="col-12 col-md-6 d-flex align-items-end">
      <button class="btn btn-warning w-100">Add Section</button>
    </div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle mb-0">
      <thead>
        <tr><th>Pos</th><th>Title</th><th>Subtitle</th><th>Status</th><th>Image</th><th></th></tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $s): ?>
          <tr>
            <td><?= (int)$s['position'] ?></td>
            <td><?= e($s['title']) ?></td>
            <td><?= e((string)$s['subtitle']) ?></td>
            <td><?= $s['status'] ? 'Active' : 'Inactive' ?></td>
            <td>
              <?php if (!empty($s['image_path'])): ?>
                <span class="small text-white-50"><?= e($s['image_path']) ?></span>
              <?php else: ?>
                <span class="small text-white-50">-</span>
              <?php endif; ?>
            </td>
            <td class="text-end">
              <form method="post" style="display:inline">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
                <button class="btn btn-sm btn-outline-warning">Toggle</button>
              </form>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete section?');">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$s['id'] ?>">
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
