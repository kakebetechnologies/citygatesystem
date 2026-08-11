-- City Gate Mixed Farm — database schema + seed data.
-- Run once: `mysql -u root < database/schema.sql` (or import via phpMyAdmin).
-- Local XAMPP dev defaults (root / no password) — change before any real deployment.

CREATE DATABASE IF NOT EXISTS citygatefarm CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE citygatefarm;

-- ============================================================
-- Users & auth
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
   id INT AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(150) NOT NULL,
   email VARCHAR(190) NOT NULL UNIQUE,
   password_hash VARCHAR(255) NOT NULL,
   role ENUM('SuperAdmin','Manager','Finance','Admin') NOT NULL,
   status ENUM('active','suspended') NOT NULL DEFAULT 'active',
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Appointments (pre-planned bookings from the public site)
-- ============================================================
CREATE TABLE IF NOT EXISTS appointments (
   id INT AUTO_INCREMENT PRIMARY KEY,
   institution VARCHAR(190) NULL,
   contact_name VARCHAR(150) NOT NULL,
   contact_phone VARCHAR(40) NOT NULL,
   contact_email VARCHAR(190) NOT NULL,
   purpose VARCHAR(60) NOT NULL,
   visit_date DATE NULL,
   visit_time VARCHAR(30) NULL,
   num_visitors INT NULL,
   message TEXT NULL,
   status ENUM('Pending','Approved','Rejected','Arrived') NOT NULL DEFAULT 'Pending',
   checked_in_at DATETIME NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS appointment_attendees (
   id INT AUTO_INCREMENT PRIMARY KEY,
   appointment_id INT NOT NULL,
   full_name VARCHAR(150) NOT NULL,
   designation VARCHAR(100) NULL,
   age VARCHAR(20) NULL,
   contact VARCHAR(100) NULL,
   FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Front-desk walk-in registry (independent of Appointments,
-- optionally linked to one when checking in a pre-booked group)
-- ============================================================
CREATE TABLE IF NOT EXISTS visitor_registry (
   id INT AUTO_INCREMENT PRIMARY KEY,
   full_name VARCHAR(150) NOT NULL,
   phone VARCHAR(40) NULL,
   email VARCHAR(190) NULL,
   purpose VARCHAR(190) NOT NULL,
   host_person VARCHAR(150) NULL,
   appointment_id INT NULL,
   signed_in_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   signed_out_at DATETIME NULL,
   recorded_by INT NULL,
   FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE SET NULL,
   FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Sales & invoicing
-- ============================================================
CREATE TABLE IF NOT EXISTS sales (
   id INT AUTO_INCREMENT PRIMARY KEY,
   product VARCHAR(150) NOT NULL,
   quantity DECIMAL(10,2) NOT NULL,
   unit VARCHAR(40) NOT NULL,
   buyer VARCHAR(150) NOT NULL,
   buyer_phone VARCHAR(40) NULL,
   amount DECIMAL(12,2) NOT NULL,
   status ENUM('Paid','Unpaid') NOT NULL DEFAULT 'Unpaid',
   sale_date DATE NOT NULL,
   created_by INT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Blog
-- ============================================================
CREATE TABLE IF NOT EXISTS blog_posts (
   id INT AUTO_INCREMENT PRIMARY KEY,
   title VARCHAR(220) NOT NULL,
   slug VARCHAR(220) NOT NULL UNIQUE,
   category VARCHAR(80) NULL,
   tags VARCHAR(255) NULL,
   excerpt TEXT NULL,
   content LONGTEXT NULL,
   featured_image VARCHAR(255) NULL,
   author_id INT NULL,
   status ENUM('draft','published','scheduled') NOT NULL DEFAULT 'draft',
   publish_date DATE NULL,
   meta_title VARCHAR(220) NULL,
   meta_description VARCHAR(320) NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS blog_post_images (
   id INT AUTO_INCREMENT PRIMARY KEY,
   post_id INT NOT NULL,
   image_path VARCHAR(255) NOT NULL,
   caption VARCHAR(255) NULL,
   sort_order INT NOT NULL DEFAULT 0,
   FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS blog_comments (
   id INT AUTO_INCREMENT PRIMARY KEY,
   post_id INT NOT NULL,
   name VARCHAR(150) NOT NULL,
   email VARCHAR(190) NULL,
   comment TEXT NOT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Product reviews (shop pages)
-- ============================================================
CREATE TABLE IF NOT EXISTS product_reviews (
   id INT AUTO_INCREMENT PRIMARY KEY,
   product_slug VARCHAR(150) NOT NULL,
   name VARCHAR(150) NOT NULL,
   rating TINYINT NOT NULL,
   comment TEXT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Gallery (photos + videos, unified)
-- ============================================================
CREATE TABLE IF NOT EXISTS gallery_media (
   id INT AUTO_INCREMENT PRIMARY KEY,
   type ENUM('photo','video') NOT NULL DEFAULT 'photo',
   file_path VARCHAR(255) NULL,
   embed_url VARCHAR(400) NULL,
   thumbnail_path VARCHAR(255) NULL,
   caption VARCHAR(255) NULL,
   category VARCHAR(80) NULL,
   sort_order INT NOT NULL DEFAULT 0,
   uploaded_by INT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- CMS editable content (page_key + field_key -> value)
-- ============================================================
CREATE TABLE IF NOT EXISTS cms_content (
   id INT AUTO_INCREMENT PRIMARY KEY,
   page_key VARCHAR(60) NOT NULL,
   field_key VARCHAR(80) NOT NULL,
   value TEXT NULL,
   updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   UNIQUE KEY uniq_page_field (page_key, field_key)
) ENGINE=InnoDB;

-- ============================================================
-- Contact form submissions
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
   id INT AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(150) NOT NULL,
   email VARCHAR(190) NULL,
   phone VARCHAR(40) NULL,
   subject VARCHAR(190) NULL,
   message TEXT NOT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Email log (single thank-yous + bulk campaigns)
-- ============================================================
CREATE TABLE IF NOT EXISTS email_log (
   id INT AUTO_INCREMENT PRIMARY KEY,
   subject VARCHAR(220) NOT NULL,
   body LONGTEXT NOT NULL,
   recipient_type ENUM('single','bulk') NOT NULL DEFAULT 'single',
   sent_by INT NULL,
   sent_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (sent_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS email_recipients (
   id INT AUTO_INCREMENT PRIMARY KEY,
   email_log_id INT NOT NULL,
   recipient_name VARCHAR(150) NULL,
   recipient_email VARCHAR(190) NOT NULL,
   status ENUM('sent','failed') NOT NULL DEFAULT 'sent',
   error_message VARCHAR(255) NULL,
   FOREIGN KEY (email_log_id) REFERENCES email_log(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Seed data
-- ============================================================

-- Demo users (passwords: super123 / manager123 / finance123 / admin123)
-- Hashes generated with PHP password_hash() using PASSWORD_DEFAULT (bcrypt).
INSERT INTO users (name, email, password_hash, role, status) VALUES
('Sarah Achen', 'superadmin@citygatefarms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'SuperAdmin', 'active'),
('David Okello', 'manager@citygatefarms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Manager', 'active'),
('Grace Auma', 'finance@citygatefarms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Finance', 'active'),
('Peter Otim', 'admin@citygatefarms.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'active')
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- NOTE: the hash above is a PLACEHOLDER shared across all 4 rows and will be
-- overwritten immediately after import by database/reset_passwords.php,
-- which sets each user's real per-role password using PHP's own
-- password_hash() at runtime (guarantees a hash PHP on this exact server
-- verifies correctly, rather than trusting a hash pasted into a .sql file).

INSERT INTO sales (product, quantity, unit, buyer, buyer_phone, amount, status, sale_date) VALUES
('Fresh Eggs', 10, 'Trays', 'Lira City Hotel', '+256 772 100 200', 120000, 'Paid', '2026-07-20'),
('Fresh Cow Milk', 50, 'Litres', 'Amuca Dairy Co-op', '+256 772 300 400', 125000, 'Paid', '2026-07-22'),
('Boer Goats', 2, 'Heads', 'John Okwir', '+256 772 500 600', 1200000, 'Unpaid', '2026-07-25'),
('Day-Old Chicks', 200, 'Chicks', 'Northern Poultry Traders', '+256 772 700 800', 2000000, 'Paid', '2026-08-01'),
('Maize', 500, 'Kg', 'Lira Grain Millers', '+256 772 900 100', 900000, 'Unpaid', '2026-08-03');

INSERT INTO blog_posts (title, slug, category, tags, excerpt, content, featured_image, author_id, status, publish_date, meta_title, meta_description) VALUES
('Scaling From 14,000 to 100,000 Birds', 'scaling-from-14000-to-100000-birds', 'Poultry', 'poultry,growth,broiler',
 'How we grew our broiler operation while keeping bird health and housing standards high.',
 '<p>Over the last two seasons we scaled our broiler operation from 14,000 to 100,000 birds by investing in clean housing, organized feeding schedules, and staff training.</p><p>The keys were phased expansion, disease-testing infrastructure, and never compromising on brooding conditions.</p>',
 'images/img38.png', 2, 'published', '2026-03-25',
 'Scaling From 14,000 to 100,000 Birds | City Gate Mixed Farm',
 'How City Gate Mixed Farm scaled its broiler operation while keeping bird health and housing standards high in Lira City, Uganda.'),
('Know the Benefit of a Boiled Egg', 'know-the-benefit-of-a-boiled-egg', 'Nutrition', 'eggs,health,nutrition',
 'A closer look at why fresh farm eggs are a simple, affordable source of daily protein.',
 '<p>A boiled egg is one of the most affordable, nutrient-dense foods available to Northern Ugandan households.</p><p>Our layer houses collect fresh eggs daily, tested and graded before they reach the shop.</p>',
 'images/img41.png', 4, 'published', '2026-04-09',
 'Know the Benefit of a Boiled Egg | City Gate Mixed Farm',
 'Why fresh farm eggs from City Gate Mixed Farm are a simple, affordable source of daily protein.'),
('Why We Started a Goat Breeding Program', 'why-we-started-a-goat-breeding-program', 'Goats', 'goats,breeding,boer',
 'Boer and Savannah goats are proving to be a resilient, profitable addition to the farm.',
 '<p>We introduced Boer and Savannah goat breeding to diversify income and demonstrate mixed farming to visiting students.</p><p>Draft post — pending final photos before publishing.</p>',
 'images/img43.png', 2, 'draft', NULL, NULL, NULL);

-- Gallery seed: the 20 real farm photos already in images/gallary/
INSERT INTO gallery_media (type, file_path, caption, category, sort_order) VALUES
('photo', 'images/gallary/9883074-chicken-3727097_1920.jpg', 'Healthy broilers in a clean house', 'Poultry', 1),
('photo', 'images/gallary/akirevarga-egg-7345934.jpg', 'Fresh eggs collected daily', 'Poultry', 2),
('photo', 'images/gallary/andreasgoellner-hen-5642953_1920.jpg', 'Layer hens at City Gate', 'Poultry', 3),
('photo', 'images/gallary/bernhardjaeck-billy-goat-7397721_1920.jpg', 'Boer billy goat', 'Goats', 4),
('photo', 'images/gallary/christoph-coffee-171653_1920.jpg', 'Coffee cherries ready for harvest', 'Crops', 5),
('photo', 'images/gallary/couleur-milk-2474993.jpg', 'Fresh cow milk', 'Dairy', 6),
('photo', 'images/gallary/die_berlinerin-animal-7189976_1920.jpg', 'Farm livestock', 'Goats', 7),
('photo', 'images/gallary/engin_akyurt-egg-6757508_1920.jpg', 'Farm-fresh eggs', 'Poultry', 8),
('photo', 'images/gallary/hat3m-seedling-4394118_1920.jpg', 'Young seedlings in the nursery', 'Crops', 9),
('photo', 'images/gallary/jaclou-dl-cow-4270355_1920.jpg', 'Dairy cow grazing', 'Dairy', 10),
('photo', 'images/gallary/marcusvu-coffee-2992598_1920.jpg', 'Coffee plot', 'Crops', 11),
('photo', 'images/gallary/neelam279-floor-7049278_1920.jpg', 'Inside the poultry house', 'Poultry', 12),
('photo', 'images/gallary/pexels-chicken-1867521_1920.jpg', 'Free-range chickens', 'Poultry', 13),
('photo', 'images/gallary/pezibear-animal-6756751_1920.jpg', 'Farm animals at pasture', 'Goats', 14),
('photo', 'images/gallary/rooster-farm.jpg', 'Rooster on the farm', 'Poultry', 15),
('photo', 'images/gallary/rschaubhut-goat-5642612_1920.jpg', 'Boer/Savannah goats', 'Goats', 16),
('photo', 'images/gallary/stevepb-nest-1050964_1920.jpg', 'Nesting boxes', 'Poultry', 17),
('photo', 'images/gallary/techvaran-goats-5902053_1920.jpg', 'Goat herd', 'Goats', 18),
('photo', 'images/gallary/walter46-goat-4138049_1920.jpg', 'Goats grazing', 'Goats', 19),
('photo', 'images/gallary/rschaubhut-goat-5642612_1920.jpg', 'City Gate goat breeding stock', 'Goats', 20);

-- CMS seed: existing 4 fields (kept blank = uses page default) + new impact stats
INSERT INTO cms_content (page_key, field_key, value) VALUES
('home', 'impact_years', '8'),
('home', 'impact_years_label', 'Years Operating'),
('home', 'impact_farmers', '300+'),
('home', 'impact_farmers_label', 'Farmers Trained'),
('home', 'impact_students', '1,200+'),
('home', 'impact_students_label', 'Students Hosted'),
('home', 'impact_sectors', '4'),
('home', 'impact_sectors_label', 'Farm Sectors')
ON DUPLICATE KEY UPDATE value = VALUES(value);
