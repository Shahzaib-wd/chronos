# CHRONOS Store (PHP + MySQL)

This project keeps the **original HTML/CSS/Vanilla JS UI** and adds a full **PHP 8+ / MySQL** backend + admin panel.

## Requirements
- PHP 8+
- MySQL 5.7+/8+
- Apache/Nginx (or XAMPP/WAMP/Laragon)

## Setup
1. Create database:

```sql
CREATE DATABASE chronos_store CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

2. Import schema then seed:
- `sql/schema.sql`
- `sql/seed.sql`

3. Configure DB credentials:
- `includes/config.php`

4. Copy placeholder images (optional but recommended):
- Already copied to `admin/assets/uploads/products/` (watch-1.jpg ... watch-4.jpg)

5. Open site:
- Frontend: `index.html`
- Admin: `/admin/login.php`

## Admin Login (seed)
- Email: `admin@example.com`
- Password: `ChangeMe123!`

## Notes
- Cart is stored in browser `localStorage` under key `chronos_cart`.
- On checkout, cart is synced to backend via `api/checkout.php`.
- Payment method: **Cash on Delivery (COD)**.
