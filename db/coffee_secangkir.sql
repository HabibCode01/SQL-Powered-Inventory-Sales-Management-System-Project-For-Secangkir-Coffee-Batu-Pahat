CREATE DATABASE IF NOT EXISTS coffee_secangkir;
USE coffee_secangkir;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Suppliers Table
CREATE TABLE suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    purchase_count INT DEFAULT 0
);

-- Ingredients Table
CREATE TABLE ingredients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    current_stock INT DEFAULT 0,
    purchased INT DEFAULT 0,
    price DECIMAL(10,2) NOT NULL
);

-- Products Table (updated with created_at column)
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50) DEFAULT 'Food',
    price DECIMAL(10,2) NOT NULL,
    sold_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Purchase Orders Table
CREATE TABLE purchase_orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ingredient_id INT,
    due_date DATE,
    supplier_id INT,
    quantity INT,
    cost DECIMAL(10,2),
    status VARCHAR(20) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ingredient_id) REFERENCES ingredients(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
);

-- Sample Data with random created_at dates for products
INSERT INTO users (name, email, password, role) VALUES
('Habib', 'anhabib2001@gmail.com', SHA2('password123', 256), 'admin'),
('Zahirah', 'zahirahahwan32@gmail.com', SHA2('password123', 256), 'manager'),
('Iera', 'mirarzak3@gmail.com', SHA2('password123', 256), 'staff'),
('Yans', 'liyananurul600@gmail.com', SHA2('password123', 256), 'staff');

INSERT INTO suppliers (name, contact_name, email, phone, purchase_count) VALUES
('Baking Room', 'Ahmad Nazmi', 'ahmadnazmi@gmail.com', '0123456789', 0),
('Coffee hubb', 'Marcus Chen', 'marcus.chen@sourdsdn@record.com', '0123456789', 3),
('CoolAid Sdn Bhd', 'Amir Khan', 'amir@booksyfi.com', '0123456789', 6),
('FarmFresh', 'Sarah Hamilton', 'sarah.hamilton@pa', '0123456789', 2),
('Jamal Incorporation', 'Safia Bamirez', 'jafis.aarbitras@movitrack.com', '0123456789', 4);

INSERT INTO ingredients (name, current_stock, purchased, price) VALUES
('Ayam Segar', 17, 0, 10.00),
('Bawang Holland', 25, 0, 4.00),
('Bihun', 32, 0, 3.00),
('Bawang Puth', 18, 0, 3.00),
('Biskut Oreo', 12, 0, 8.00),
('Cendawan', 20, 0, 4.00);

INSERT INTO products (name, category, price, sold_count, created_at) VALUES
('Chicken Soup', 'Food', 11.90, 2, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY)),
('Seafood Tom Yum', 'Food', 12.00, 3, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY)),
('Kuey Teow Goreng', 'Food', 15.90, 4, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY)),
('Nasi Goreng Kampung', 'Food', 11.90, 4, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY)),
('Nasi Goreng Cina', 'Food', 10.90, 7, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY)),
('Nasi Goreng Kampung Chicken Chop Grill', 'Food', 20.90, 4, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY)),
('Nasi Goreng Cina Chicken Chop Crispy', 'Food', 21.90, 5, DATE_SUB(NOW(), INTERVAL FLOOR(RAND() * 180) DAY));

INSERT INTO purchase_orders (ingredient_id, due_date, supplier_id, quantity, cost) VALUES
(1, '2025-06-24', 5, 12, 1.00),
(2, '2025-06-18', 4, 18, 54.00),
(3, '2025-06-18', 3, 5, 150.00),
(4, '2025-06-16', 5, 12, 360.00),
(5, '2025-06-25', 2, 12, 36.00),
(6, '2025-06-18', 5, 10, 30.00);