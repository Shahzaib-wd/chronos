<?php
// Helper: generate bcrypt hash for a password
// Usage: php -S localhost:8000 then open /admin/tools/hash.php?pw=ChangeMe123!

declare(strict_types=1);

$pw = (string)($_GET['pw'] ?? '');
if ($pw === '') {
  header('Content-Type: text/plain; charset=utf-8');
  echo "Provide ?pw=...";
  exit;
}

header('Content-Type: text/plain; charset=utf-8');
echo password_hash($pw, PASSWORD_BCRYPT);
