<?php
declare(strict_types=1);
require_once __DIR__ . '/auth.php';
require_admin();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - CHRONOS</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#0F1419;color:#fff;}
    .sidebar{background:#111826;min-height:100vh;border-right:1px solid rgba(255,255,255,.08)}
    a{color:#fff;text-decoration:none}
    .nav-link{color:rgba(255,255,255,.8)}
    .nav-link.active,.nav-link:hover{color:#D4AF37}
    .card{background:#111826;border:1px solid rgba(255,255,255,.08)}
    .text-gold{color:#D4AF37}
    .table{color:#fff}
    .form-control,.form-select{background:#0F1419;border:1px solid rgba(255,255,255,.12);color:#fff}
    .form-control:focus,.form-select:focus{border-color:#D4AF37;box-shadow:none}
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <aside class="col-12 col-lg-2 sidebar p-3">
      <div class="mb-4">
        <div class="h5 mb-0"><span class="text-gold">CHRONOS</span></div>
        <div class="small text-white-50">Admin Panel</div>
      </div>
      <nav class="nav flex-column gap-1">
        <a class="nav-link" href="index.php">Dashboard</a>
        <a class="nav-link" href="products.php">Products</a>
        <a class="nav-link" href="categories.php">Categories</a>
        <a class="nav-link" href="brands.php">Brands</a>
        <a class="nav-link" href="homepage_sections.php">Homepage Sections</a>
        <a class="nav-link" href="about_page.php">About Page</a>
        <a class="nav-link" href="contact_messages.php">Contact Messages</a>
        <a class="nav-link" href="orders.php">Orders</a>
        <a class="nav-link" href="settings.php">Store Settings</a>
        <a class="nav-link" href="logout.php">Logout</a>
      </nav>
    </aside>
    <main class="col-12 col-lg-10 p-4">
