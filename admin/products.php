<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/header.php';

$pdo = db();
$msg = '';

function safe_upload_product_images(string $field, string $destDir): array {
    $out = [];
    if (empty($_FILES[$field]) || !isset($_FILES[$field]['name']) || !is_array($_FILES[$field]['name'])) return $out;
    $allowed = ['image/jpeg','image/png','image/webp'];
    if (!is_dir($destDir)) mkdir($destDir, 0775, true);

    $count = count($_FILES[$field]['name']);
    for ($i=0; $i<$count; $i++) {
        if ($_FILES[$field]['error'][$i] !== UPLOAD_ERR_OK) continue;
        $tmp = $_FILES[$field]['tmp_name'][$i];
        $mime = mime_content_type($tmp) ?: '';
        if (!in_array($mime, $allowed, true)) continue;
        $ext = match($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg'
        };
        $name = bin2hex(random_bytes(8)) . '.' . $ext;
        $path = rtrim($destDir,'/') . '/' . $name;
        if (move_uploaded_file($tmp, $path)) $out[] = $name;
    }
    return $out;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!csrf_validate($_POST['csrf'] ?? null)) {
        $msg = 'CSRF validation failed.';
    } else {
        $action = $_POST['action'] ?? '';
        if ($action === 'create') {
            $name = trim((string)($_POST['name'] ?? ''));
            $sku = trim((string)($_POST['sku'] ?? ''));
            $brand_id = (int)($_POST['brand_id'] ?? 0);
            $category_id = (int)($_POST['category_id'] ?? 0);
            $desc = (string)($_POST['description_html'] ?? '');
            $price = (float)($_POST['price'] ?? 0);
            $discount = trim((string)($_POST['discount_price'] ?? ''));
            $discount_price = $discount === '' ? null : (float)$discount;
            $stock = (int)($_POST['stock_qty'] ?? 0);
            $is_featured = isset($_POST['is_featured']) ? 1 : 0;
            $status = isset($_POST['status']) ? 1 : 0;

            $stmt = $pdo->prepare('INSERT INTO products (name, sku, brand_id, category_id, description_html, price, discount_price, stock_qty, is_featured, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())');
            $stmt->execute([$name, $sku, $brand_id, $category_id, $desc, $price, $discount_price, $stock, $is_featured, $status]);
            $pid = (int)$pdo->lastInsertId();

            $imgs = safe_upload_product_images('images', PRODUCT_UPLOAD_DIR);
            if ($imgs) {
                $imgStmt = $pdo->prepare('INSERT INTO product_images (product_id, image_path, sort_order) VALUES (?, ?, ?)');
                $i=0;
                foreach ($imgs as $fn) {
                    $imgStmt->execute([$pid, $fn, $i++]);
                }
            }
            $msg = 'Product created.';
        }
        if ($action === 'delete') {
            $id = (int)($_POST['id'] ?? 0);
            // delete images from disk
            $stmt = $pdo->prepare('SELECT image_path FROM product_images WHERE product_id=?');
            $stmt->execute([$id]);
            foreach ($stmt->fetchAll() as $im) {
                $fp = PRODUCT_UPLOAD_DIR . '/' . $im['image_path'];
                if (is_file($fp)) @unlink($fp);
            }
            $pdo->prepare('DELETE FROM products WHERE id=?')->execute([$id]);
            $msg = 'Product deleted.';
        }
        if ($action === 'toggle') {
            $id = (int)($_POST['id'] ?? 0);
            $pdo->prepare('UPDATE products SET status = 1 - status WHERE id=?')->execute([$id]);
            $msg = 'Product status updated.';
        }
    }
}

$brands = $pdo->query('SELECT id,name FROM brands WHERE status=1 ORDER BY name ASC')->fetchAll();
$cats = $pdo->query('SELECT id,name FROM categories WHERE status=1 ORDER BY name ASC')->fetchAll();

$products = $pdo->query('SELECT p.id,p.name,p.sku,p.price,p.discount_price,p.stock_qty,p.status,p.is_featured,b.name AS brand,c.name AS category
                         FROM products p JOIN brands b ON b.id=p.brand_id JOIN categories c ON c.id=p.category_id
                         ORDER BY p.id DESC')->fetchAll();
?>
<h2 class="mb-3">Products</h2>
<?php if ($msg): ?><div class="alert alert-info"><?= e($msg) ?></div><?php endif; ?>

<div class="card p-3 mb-3">
  <div class="text-white-50 mb-2">Add Product</div>
  <form method="post" enctype="multipart/form-data">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <input type="hidden" name="action" value="create">
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label class="form-label">Name</label>
        <input class="form-control" name="name" required>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">SKU</label>
        <input class="form-control" name="sku" required>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Stock Qty</label>
        <input class="form-control" type="number" name="stock_qty" value="0">
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Brand</label>
        <select class="form-select" name="brand_id" required>
          <?php foreach ($brands as $b): ?>
            <option value="<?= (int)$b['id'] ?>"><?= e($b['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Category</label>
        <select class="form-select" name="category_id" required>
          <?php foreach ($cats as $c): ?>
            <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Price (PKR)</label>
        <input class="form-control" type="number" step="0.01" name="price" required>
      </div>
      <div class="col-12 col-md-3">
        <label class="form-label">Discount Price (optional)</label>
        <input class="form-control" type="number" step="0.01" name="discount_price">
      </div>
      <div class="col-12">
        <label class="form-label">Description (HTML allowed)</label>
        <textarea class="form-control" name="description_html" rows="4"></textarea>
      </div>
      <div class="col-12 col-md-6">
        <label class="form-label">Images (multiple)</label>
        <input class="form-control" type="file" name="images[]" accept="image/*" multiple>
      </div>
      <div class="col-12 col-md-6">
        <div class="form-check mt-4">
          <input class="form-check-input" type="checkbox" name="is_featured" checked>
          <label class="form-check-label">Featured</label>
        </div>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="status" checked>
          <label class="form-check-label">Active</label>
        </div>
      </div>
      <div class="col-12">
        <button class="btn btn-warning">Create Product</button>
      </div>
    </div>
  </form>
</div>

<div class="card p-3">
  <div class="table-responsive">
    <table class="table table-dark table-striped align-middle mb-0">
      <thead>
        <tr>
          <th>ID</th><th>Name</th><th>SKU</th><th>Brand</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td><?= (int)$p['id'] ?></td>
            <td><?= e($p['name']) ?> <?= $p['is_featured']?'<span class="badge bg-warning text-dark ms-1">Featured</span>':'' ?></td>
            <td><?= e($p['sku']) ?></td>
            <td><?= e($p['brand']) ?></td>
            <td><?= e($p['category']) ?></td>
            <td>
              Rs. <?= e(number_format((float)($p['discount_price'] ?? $p['price']),0,'.',',')) ?>
              <?php if ($p['discount_price'] !== null): ?>
                <span class="small text-white-50 text-decoration-line-through">Rs. <?= e(number_format((float)$p['price'],0,'.',',')) ?></span>
              <?php endif; ?>
            </td>
            <td><?= (int)$p['stock_qty'] ?></td>
            <td><?= $p['status'] ? 'Active' : 'Inactive' ?></td>
            <td class="text-end">
              <form method="post" style="display:inline">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn-sm btn-outline-warning">Toggle</button>
              </form>
              <form method="post" style="display:inline" onsubmit="return confirm('Delete product?');">
                <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
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
