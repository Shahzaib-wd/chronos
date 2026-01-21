<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/auth.php';

if (admin_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $email = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    $stmt = db()->prepare('SELECT id, email, password_hash, name FROM admins WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password_hash'])) {
        $_SESSION['admin_id'] = (int)$admin['id'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['name'];
        // rotate session id
        session_regenerate_id(true);
        header('Location: index.php');
        exit;
    }
    $error = 'Invalid email or password';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Login - CHRONOS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#0F1419;color:#fff;min-height:100vh;display:flex;align-items:center;}
    .card{background:#111826;border:1px solid rgba(255,255,255,.08)}
    .text-gold{color:#D4AF37}
  </style>
</head>
<body>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 col-lg-4">
        <div class="text-center mb-4">
          <h2 class="mb-1"><span class="text-gold">CHRONOS</span> Admin</h2>
          <p class="text-white-50 mb-0">Login to manage store</p>
        </div>
        <div class="card p-4">
          <?php if ($error): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
          <?php endif; ?>
          <form method="post" autocomplete="off">
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
            </div>
            <button class="btn btn-warning w-100" type="submit">Login</button>
          </form>
          <div class="mt-3 small text-white-50">
            Default: admin@example.com / ChangeMe123!
          </div>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
