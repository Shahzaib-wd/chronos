# Setup Guide (Local)

## 1) Database
Create DB and import SQL:

- `sql/schema.sql`
- `sql/seed.sql`

## 2) Configure DB
Edit `includes/config.php`:

- DB_HOST, DB_NAME, DB_USER, DB_PASS

## 3) Run
Place project in your server root (e.g. `htdocs/chronos_store`).

Open:
- Frontend: `/index.html`
- Admin: `/admin/login.php`

## 4) Default Admin
- admin@example.com
- ChangeMe123!

If you ever need to regenerate the bcrypt hash on your server:
- Visit: `/admin/tools/hash.php?pw=ChangeMe123!`
