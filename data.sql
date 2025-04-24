-- =========================
-- z1960727 Justin Carney  |
-- z2051554 Aasim Ghani    |
-- Tyler Rouw 21942888     |
-- Liam Belh z2047328      |
-- Trevor Jannsen z2036452 |
-- =========================

-- Insert 20 Products
INSERT INTO Product (name, description, price, stock_quantity) VALUES
('Laptop', '16GB RAM, 512GB SSD', 999.99, 50),
('Smartphone', '6.5" OLED, 128GB', 699.99, 100),
...; -- Add 18 more products

-- Insert 5 Customers
INSERT INTO User (username, email, password_hash, role) VALUES
('alice', 'alice@example.com', '$2y$10$hashed123', 'customer'),
('bob', 'bob@example.com', '$2y$10$hashed456', 'customer'),
...; -- Add 3 more customers

-- Insert Orders (1 per customer)
INSERT INTO `Order` (user_id, total_amount, shipping_address) VALUES
(1, 1699.98, '123 Main St'),
(2, 899.99, '456 Oak St'),
...;

-- Insert OrderDetails
INSERT INTO OrderDetail (order_id, product_id, quantity, price_at_purchase) VALUES
(1, 1, 1, 999.99),
(1, 2, 1, 699.99),
...;