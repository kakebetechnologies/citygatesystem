-- City Gate Mixed Farm — Farm Management Suite (v2 schema addition).
-- Adds 8 tables on top of the existing citygatefarm database + seeds
-- realistic starting data. Safe to run once against an already-imported
-- database/schema.sql: `mysql -u root citygatefarm < database/schema_v2_farm_management.sql`

USE citygatefarm;

-- ============================================================
-- Livestock — one flexible model: a poultry "batch" or an
-- individual goat/dairy animal, distinguished by `sector`.
-- ============================================================
CREATE TABLE IF NOT EXISTS livestock_records (
   id INT AUTO_INCREMENT PRIMARY KEY,
   sector ENUM('Poultry','Goats','Dairy') NOT NULL,
   identifier VARCHAR(60) NOT NULL UNIQUE,
   breed VARCHAR(100) NULL,
   sex ENUM('Male','Female') NULL,
   quantity INT NOT NULL DEFAULT 1,
   date_acquired DATE NOT NULL,
   source ENUM('Hatched','Purchased','Born') NOT NULL DEFAULT 'Purchased',
   status ENUM('Active','Sold','Deceased') NOT NULL DEFAULT 'Active',
   notes TEXT NULL,
   created_by INT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
   FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS livestock_health_records (
   id INT AUTO_INCREMENT PRIMARY KEY,
   livestock_id INT NOT NULL,
   record_type ENUM('Vaccination','Treatment','Checkup','Incident') NOT NULL,
   description TEXT NOT NULL,
   record_date DATE NOT NULL,
   next_due_date DATE NULL,
   cost DECIMAL(12,2) NULL,
   performed_by VARCHAR(150) NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (livestock_id) REFERENCES livestock_records(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Inventory — feed / medicine / equipment stock.
-- ============================================================
CREATE TABLE IF NOT EXISTS inventory_items (
   id INT AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(150) NOT NULL,
   category ENUM('Feed','Medicine','Equipment','Other') NOT NULL,
   unit VARCHAR(40) NOT NULL,
   quantity_on_hand DECIMAL(12,2) NOT NULL DEFAULT 0,
   reorder_level DECIMAL(12,2) NOT NULL DEFAULT 0,
   unit_cost DECIMAL(12,2) NULL,
   notes TEXT NULL,
   updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS inventory_transactions (
   id INT AUTO_INCREMENT PRIMARY KEY,
   item_id INT NOT NULL,
   type ENUM('In','Out') NOT NULL,
   quantity DECIMAL(12,2) NOT NULL,
   reason VARCHAR(255) NULL,
   related_livestock_id INT NULL,
   performed_by INT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (item_id) REFERENCES inventory_items(id) ON DELETE CASCADE,
   FOREIGN KEY (related_livestock_id) REFERENCES livestock_records(id) ON DELETE SET NULL,
   FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Production records (eggs, milk, weight checks, harvests).
-- ============================================================
CREATE TABLE IF NOT EXISTS production_records (
   id INT AUTO_INCREMENT PRIMARY KEY,
   sector ENUM('Poultry','Dairy','Crops','Goats') NOT NULL,
   metric VARCHAR(60) NOT NULL,
   quantity DECIMAL(12,2) NOT NULL,
   unit VARCHAR(40) NOT NULL,
   record_date DATE NOT NULL,
   livestock_id INT NULL,
   notes TEXT NULL,
   recorded_by INT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (livestock_id) REFERENCES livestock_records(id) ON DELETE SET NULL,
   FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Expenses (pairs with the existing `sales` table for P&L).
-- ============================================================
CREATE TABLE IF NOT EXISTS suppliers_buyers (
   id INT AUTO_INCREMENT PRIMARY KEY,
   name VARCHAR(150) NOT NULL,
   type ENUM('Supplier','Buyer','Both') NOT NULL DEFAULT 'Supplier',
   contact_person VARCHAR(150) NULL,
   phone VARCHAR(40) NULL,
   email VARCHAR(190) NULL,
   address VARCHAR(255) NULL,
   category VARCHAR(80) NULL,
   notes TEXT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS expenses (
   id INT AUTO_INCREMENT PRIMARY KEY,
   category ENUM('Feed','Labor','Veterinary','Utilities','Repairs','Transport','Other') NOT NULL,
   description VARCHAR(255) NOT NULL,
   amount DECIMAL(12,2) NOT NULL,
   expense_date DATE NOT NULL,
   supplier_id INT NULL,
   recorded_by INT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   FOREIGN KEY (supplier_id) REFERENCES suppliers_buyers(id) ON DELETE SET NULL,
   FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Tasks (feeding/vaccination/cleaning schedules, staff-assigned).
-- ============================================================
CREATE TABLE IF NOT EXISTS tasks (
   id INT AUTO_INCREMENT PRIMARY KEY,
   title VARCHAR(200) NOT NULL,
   description TEXT NULL,
   category VARCHAR(60) NULL,
   due_date DATE NOT NULL,
   due_time TIME NULL,
   assigned_to INT NULL,
   status ENUM('Pending','Done') NOT NULL DEFAULT 'Pending',
   recurrence_label VARCHAR(40) NULL,
   livestock_id INT NULL,
   created_by INT NULL,
   created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
   completed_at DATETIME NULL,
   FOREIGN KEY (assigned_to) REFERENCES users(id) ON DELETE SET NULL,
   FOREIGN KEY (livestock_id) REFERENCES livestock_records(id) ON DELETE SET NULL,
   FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- Seed data
-- ============================================================

INSERT INTO livestock_records (sector, identifier, breed, sex, quantity, date_acquired, source, status, notes, created_by) VALUES
('Poultry', 'PLT-2026-06-01', 'Broiler — Cobb 500', NULL, 5000, '2026-06-01', 'Purchased', 'Active', 'Main broiler house, batch 1.', 2),
('Poultry', 'PLT-2026-07-15', 'Layer — Bovans Brown', NULL, 3000, '2026-07-15', 'Purchased', 'Active', 'Layer house, egg production.', 2),
('Poultry', 'PLT-2026-08-01', 'Broiler — Cobb 500', NULL, 2000, '2026-08-01', 'Hatched', 'Active', 'Own hatchery batch.', 2),
('Goats', 'GT-001', 'Boer', 'Male', 1, '2025-11-10', 'Purchased', 'Active', 'Breeding buck.', 2),
('Goats', 'GT-002', 'Boer', 'Female', 1, '2025-11-10', 'Purchased', 'Active', NULL, 2),
('Goats', 'GT-003', 'Savannah', 'Female', 1, '2026-02-20', 'Born', 'Active', 'Born on farm.', 2),
('Goats', 'GT-004', 'Boer', 'Female', 1, '2026-01-05', 'Purchased', 'Sold', 'Sold to Kitgum Livestock Traders.', 2),
('Dairy', 'DC-001', 'Friesian', 'Female', 1, '2024-08-01', 'Purchased', 'Active', 'Primary milker.', 2),
('Dairy', 'DC-002', 'Friesian x Jersey', 'Female', 1, '2025-03-15', 'Born', 'Active', NULL, 2),
('Dairy', 'DC-003', 'Ankole', 'Female', 1, '2024-11-20', 'Purchased', 'Active', NULL, 2);

INSERT INTO livestock_health_records (livestock_id, record_type, description, record_date, next_due_date, cost, performed_by) VALUES
((SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 'Vaccination', 'Newcastle Disease vaccine — full batch dose', '2026-07-16', '2026-10-16', 150000, 'Dr. Okello Vet Services'),
((SELECT id FROM livestock_records WHERE identifier='PLT-2026-08-01'), 'Vaccination', 'Gumboro (IBD) vaccine', '2026-08-03', '2026-08-24', 90000, 'Dr. Okello Vet Services'),
((SELECT id FROM livestock_records WHERE identifier='PLT-2026-06-01'), 'Checkup', 'Pre-slaughter health check, batch cleared healthy', '2026-08-05', NULL, NULL, 'David Okello'),
((SELECT id FROM livestock_records WHERE identifier='GT-001'), 'Checkup', 'Routine deworming', '2026-07-01', '2026-10-01', 20000, 'David Okello'),
((SELECT id FROM livestock_records WHERE identifier='DC-001'), 'Treatment', 'Mastitis treatment — antibiotic course', '2026-06-10', NULL, 45000, 'Dr. Okello Vet Services'),
((SELECT id FROM livestock_records WHERE identifier='DC-002'), 'Vaccination', 'Foot-and-mouth disease (FMD) vaccine', '2026-05-20', '2026-11-20', 60000, 'Dr. Okello Vet Services');

INSERT INTO inventory_items (name, category, unit, quantity_on_hand, reorder_level, unit_cost, notes) VALUES
('Layer Mash', 'Feed', 'Kg', 800, 200, 2200, NULL),
('Broiler Starter Feed', 'Feed', 'Kg', 150, 200, 2800, 'Running low — reorder soon.'),
('Maize Bran', 'Feed', 'Kg', 500, 150, 1200, NULL),
('Newcastle Vaccine', 'Medicine', 'Doses', 40, 50, 500, 'Running low — reorder soon.'),
('Dewormer', 'Medicine', 'Bottles', 12, 5, 15000, NULL),
('Wheelbarrow', 'Equipment', 'Units', 3, 1, 180000, NULL),
('Egg Trays', 'Equipment', 'Pieces', 300, 100, 1500, NULL);

INSERT INTO inventory_transactions (item_id, type, quantity, reason, performed_by, created_at) VALUES
((SELECT id FROM inventory_items WHERE name='Layer Mash'), 'In', 1000, 'Monthly restock from Northern Feeds Ltd', 2, '2026-07-28 09:00:00'),
((SELECT id FROM inventory_items WHERE name='Layer Mash'), 'Out', 200, 'Daily feeding — layer house', 2, '2026-08-08 07:00:00'),
((SELECT id FROM inventory_items WHERE name='Broiler Starter Feed'), 'In', 400, 'Restock', 2, '2026-07-05 09:00:00'),
((SELECT id FROM inventory_items WHERE name='Broiler Starter Feed'), 'Out', 250, 'Feeding — broiler batch PLT-2026-08-01', 2, '2026-08-06 07:00:00'),
((SELECT id FROM inventory_items WHERE name='Newcastle Vaccine'), 'Out', 60, 'Vaccination round — layer batch', 2, '2026-07-16 10:00:00');

INSERT INTO production_records (sector, metric, quantity, unit, record_date, livestock_id, recorded_by) VALUES
('Poultry', 'Eggs Collected', 96, 'Trays', '2026-08-01', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 2),
('Poultry', 'Eggs Collected', 98, 'Trays', '2026-08-02', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 2),
('Poultry', 'Eggs Collected', 94, 'Trays', '2026-08-03', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 2),
('Poultry', 'Eggs Collected', 101, 'Trays', '2026-08-05', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 2),
('Poultry', 'Eggs Collected', 99, 'Trays', '2026-08-07', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 2),
('Poultry', 'Eggs Collected', 103, 'Trays', '2026-08-09', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 2),
('Dairy', 'Milk Yield', 27.5, 'Litres', '2026-08-01', (SELECT id FROM livestock_records WHERE identifier='DC-001'), 2),
('Dairy', 'Milk Yield', 26.0, 'Litres', '2026-08-03', (SELECT id FROM livestock_records WHERE identifier='DC-001'), 2),
('Dairy', 'Milk Yield', 28.0, 'Litres', '2026-08-05', (SELECT id FROM livestock_records WHERE identifier='DC-001'), 2),
('Dairy', 'Milk Yield', 24.0, 'Litres', '2026-08-07', (SELECT id FROM livestock_records WHERE identifier='DC-002'), 2),
('Dairy', 'Milk Yield', 25.5, 'Litres', '2026-08-09', (SELECT id FROM livestock_records WHERE identifier='DC-002'), 2),
('Poultry', 'Weight Gain', 1.8, 'Kg avg/bird', '2026-08-01', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-06-01'), 2),
('Poultry', 'Weight Gain', 2.3, 'Kg avg/bird', '2026-08-08', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-06-01'), 2),
('Crops', 'Harvest', 85, 'Kg', '2026-07-20', NULL, 2),
('Crops', 'Harvest', 60, 'Bunches', '2026-07-28', NULL, 2);

INSERT INTO suppliers_buyers (name, type, contact_person, phone, email, address, category, notes) VALUES
('Northern Feeds Ltd', 'Supplier', 'Timothy Ocaya', '+256 772 441 990', 'sales@northernfeeds.ug', 'Industrial Area, Lira City', 'Feed Supplier', 'Main feed supplier — monthly restock terms.'),
('Uganda Veterinary Services — Lira Branch', 'Supplier', 'Dr. Okello', '+256 782 336 771', 'lira@uvs.ug', 'Lira City', 'Veterinary', 'Vaccines, treatments, and routine checkups.'),
('Lira City Hotel', 'Buyer', 'Procurement Office', '+256 772 100 200', 'procurement@liracityhotel.com', 'Lira City', 'Produce Buyer', 'Regular weekly egg + milk order.'),
('Savannah Grill Restaurant', 'Buyer', 'Kitchen Manager', '+256 772 118 400', NULL, 'Lira City', 'Produce Buyer', 'Bulk broiler chicken buyer.'),
('Amuca Dairy Co-op', 'Both', 'Chairperson', '+256 772 300 400', NULL, 'Amuca, Lira City', 'Dairy', 'Buys surplus milk, occasionally sells feed supplements.'),
('Kitgum Livestock Traders', 'Buyer', 'Manager', '+256 782 118 552', NULL, 'Kitgum', 'Livestock Buyer', 'Regular goat buyer.');

INSERT INTO expenses (category, description, amount, expense_date, supplier_id, recorded_by) VALUES
('Feed', 'Broiler starter feed restock', 850000, '2026-07-28', (SELECT id FROM suppliers_buyers WHERE name='Northern Feeds Ltd'), 3),
('Veterinary', 'Newcastle vaccine batch + vet callout', 320000, '2026-07-16', (SELECT id FROM suppliers_buyers WHERE name='Uganda Veterinary Services — Lira Branch'), 3),
('Labor', 'July casual labor — poultry house cleaning', 400000, '2026-07-31', NULL, 3),
('Utilities', 'Electricity bill — July', 180000, '2026-08-01', NULL, 3),
('Repairs', 'Poultry house roof repair', 250000, '2026-07-10', NULL, 3),
('Transport', 'Fuel — feed delivery truck', 120000, '2026-08-05', NULL, 3),
('Feed', 'Layer mash restock', 600000, '2026-08-08', (SELECT id FROM suppliers_buyers WHERE name='Northern Feeds Ltd'), 3);

INSERT INTO tasks (title, description, category, due_date, assigned_to, status, recurrence_label, livestock_id, created_by, completed_at) VALUES
('Vaccinate layer batch — Newcastle booster', 'Follow-up Newcastle dose due for PLT-2026-07-15.', 'Vaccination', '2026-10-16', 2, 'Pending', 'Quarterly', (SELECT id FROM livestock_records WHERE identifier='PLT-2026-07-15'), 1, NULL),
('Clean broiler house B', 'Full wash-down and disinfect ahead of next batch.', 'Cleaning', '2026-08-11', 2, 'Pending', 'Weekly', NULL, 1, NULL),
('Deworm goat herd', 'Routine deworming for all active goats.', 'Health', '2026-08-05', 2, 'Pending', 'Quarterly', NULL, 1, NULL),
('Restock layer mash', 'Order and receive next layer mash delivery.', 'Feeding', '2026-08-08', 2, 'Done', NULL, NULL, 1, '2026-08-08 15:20:00'),
('Repair broiler house fence', 'Section near the east gate needs new wire mesh.', 'Repairs', '2026-08-15', 2, 'Pending', NULL, NULL, 1, NULL),
('Morning egg collection & grading', 'Daily collection, grading, and tray count.', 'Feeding', '2026-08-10', 2, 'Pending', 'Daily', NULL, 1, NULL),
('Milk collection & cooling check', 'Morning milking, record yield, verify cooler temperature.', 'Feeding', '2026-08-10', 2, 'Pending', 'Daily', NULL, 1, NULL);
