-- Table for users
CREATE TABLE users (
    userID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('employee', 'customer', 'owner') NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20)
    
);

-- Table for products
CREATE TABLE products (
    prodID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    image VARCHAR(255) NOT NULL,
    stockInUse INT DEFAULT 0
);

-- Table for carts
CREATE TABLE carts (
    cartID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    userID INT NOT NULL UNIQUE,
    FOREIGN KEY (userID) REFERENCES users(userID)
);

-- Table to store info on items within carts
CREATE TABLE cartItems (
    itemID INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    cartID INT NOT NULL,
    prodID INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (cartID) REFERENCES carts(cartID),
    FOREIGN KEY (prodID) REFERENCES products(prodID)
);

-- Table for orders
CREATE TABLE orders (
    orderID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    userID INT NOT NULL,
    status ENUM('processing','shipped') DEFAULT 'processing',
    orderDate DATETIME DEFAULT CURRENT_TIMESTAMP,
    trackingNumber VARCHAR(20),
    orderTotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    notes TEXT,
    FOREIGN KEY (userID) REFERENCES users(userID)
);

-- Table to store info on items on orders
CREATE TABLE orderItems (
    orderItemID INT PRIMARY KEY NOT NULL AUTO_INCREMENT,
    orderID INT NOT NULL,
    prodID INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (orderID) REFERENCES orders(orderID),
    FOREIGN KEY (prodID) REFERENCES products(prodID)
);

