-- Database schema for aluminium sales website

CREATE DATABASE IF NOT EXISTS aluminium_sales;
USE aluminium_sales;

-- Table for users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    address TEXT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    stock INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert example products
INSERT INTO products (name, description, stock, price) VALUES
('Pintu Aluminium', 'Pintu aluminium berkualitas tinggi', 50, 1500000.00),
('Jendela Aluminium', 'Jendela aluminium tahan cuaca', 75, 1200000.00),
('Lemari Aluminium', 'Lemari aluminium multifungsi', 30, 2500000.00),
('Rak Aluminium', 'Rak aluminium untuk penyimpanan', 40, 800000.00);

-- Table for transactions
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    transaction_number VARCHAR(50) NOT NULL UNIQUE,
    transaction_date DATE NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    buyer_contact VARCHAR(100) NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);
