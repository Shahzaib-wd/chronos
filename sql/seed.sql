-- Seed data for CHRONOS Store

INSERT INTO settings (`key`, `value`) VALUES
('store_name', 'CHRONOS'),
('currency', 'PKR'),
('support_phone', '+92-300-0000000'),
('support_email', 'support@example.com'),
('store_address', 'The Gold Tower, Phase 6, DHA, Karachi, Pakistan')
ON DUPLICATE KEY UPDATE `value`=VALUES(`value`);

INSERT INTO admins (email, password_hash, name)
VALUES ('admin@example.com', '$2y$10$RTMFMyeeJdGwd5cwD0/6iuIifi2W/MUNkTbwEiKR/FXL.JKe9o8Eu', 'Default Admin')
ON DUPLICATE KEY UPDATE email=email;
-- NOTE: password hash above is bcrypt for "ChangeMe123!" (seeded)

INSERT INTO brands (name, slug, status) VALUES
('Chronos', 'chronos', 1),
('Aurum', 'aurum', 1),
('Noir', 'noir', 1)
ON DUPLICATE KEY UPDATE slug=VALUES(slug), status=VALUES(status);

INSERT INTO categories (name, slug, status) VALUES
('Men', 'men', 1),
('Women', 'women', 1),
('Unisex', 'unisex', 1)
ON DUPLICATE KEY UPDATE slug=VALUES(slug), status=VALUES(status);

-- Products (use existing images from /assets as placeholders; admin can upload later)
INSERT INTO products (name, sku, brand_id, category_id, description_html, price, discount_price, stock_qty, is_featured, status)
VALUES
('Prestige Chronograph', 'CHR-PRST-001', (SELECT id FROM brands WHERE slug='chronos' LIMIT 1), (SELECT id FROM categories WHERE slug='men' LIMIT 1),
 '<p>Precision chronograph with stainless steel strap. Designed for everyday luxury.</p>', 4899, NULL, 12, 1, 1),
('Heritage Rose Gold', 'CHR-HRTG-002', (SELECT id FROM brands WHERE slug='chronos' LIMIT 1), (SELECT id FROM categories WHERE slug='unisex' LIMIT 1),
 '<p>Classic rose-gold finish with leather strap. Perfect for formal occasions.</p>', 3999, 3299, 5, 1, 1),
('Midnight Eclipse', 'CHR-MDNT-003', (SELECT id FROM brands WHERE slug='chronos' LIMIT 1), (SELECT id FROM categories WHERE slug='men' LIMIT 1),
 '<p>Minimal black dial with mesh strap and premium finish.</p>', 2499, NULL, 18, 1, 1),
('Diamond Elegance', 'CHR-DMND-004', (SELECT id FROM brands WHERE slug='chronos' LIMIT 1), (SELECT id FROM categories WHERE slug='women' LIMIT 1),
 '<p>Elegant silhouette with gold bracelet and diamond accents.</p>', 5999, NULL, 3, 1, 1)
ON DUPLICATE KEY UPDATE sku=sku;

-- Product Images (copy images into admin uploads at setup step or keep blank; this seeds filenames)
INSERT INTO product_images (product_id, image_path, sort_order)
SELECT p.id, img.image_path, img.sort_order
FROM (
  SELECT 'CHR-PRST-001' AS sku, 'watch-1.jpg' AS image_path, 0 AS sort_order
  UNION ALL SELECT 'CHR-HRTG-002','watch-2.jpg',0
  UNION ALL SELECT 'CHR-MDNT-003','watch-3.jpg',0
  UNION ALL SELECT 'CHR-DMND-004','watch-4.jpg',0
) img
JOIN products p ON p.sku = img.sku
ON DUPLICATE KEY UPDATE image_path = VALUES(image_path),
                        sort_order = VALUES(sort_order);

INSERT INTO about_page (id, title, content_html, updated_at)
VALUES (1, 'About CHRONOS', '<p>CHRONOS crafts timeless elegance with a focus on precision, materials, and design heritage.</p>', NOW())
ON DUPLICATE KEY UPDATE title=VALUES(title), content_html=VALUES(content_html), updated_at=NOW();

INSERT INTO homepage_sections (title, subtitle, content_html, image_path, position, status)
VALUES
('Limited Editions', 'Collectors’ Picks', '<p>Discover rare drops and limited-run designs curated for true enthusiasts.</p>', NULL, 10, 1),
('Care & Warranty', 'Confidence Included', '<p>Lifetime warranty support and guidance to keep your timepiece perfect.</p>', NULL, 20, 1),
('Pakistan Delivery', 'Fast & Insured', '<p>Fully insured express delivery across Pakistan with secure packaging.</p>', NULL, 30, 1)
ON DUPLICATE KEY UPDATE title=VALUES(title);
