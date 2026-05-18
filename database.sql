CREATE DATABASE CarRentDb;
USE CarRentDb;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin','member') NOT NULL DEFAULT 'member',
    profile_picture VARCHAR(255) DEFAULT NULL,
    address TEXT DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    type ENUM('Private car','Microbus','Pic-kUp','SUV','Van','Sedan') NOT NULL,
    price_per_day DECIMAL(10,2) NOT NULL,
    availability_status ENUM('available','unavailable') DEFAULT 'available',
    image_path VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    status ENUM('pending','confirmed','cancelled') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT NULL,
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('CreditCard','Bkash','Nagad','Bankransfer','CashOnDelivery') NOT NULL,
    transaction_id VARCHAR(100) DEFAULT NULL,
    payment_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
);

CREATE TABLE blogs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO cars (name, model, type, price_per_day, availability_status, image_path, description) VALUES
('Toyota Corolla',      '2023', 'Private car', 2500.00, 'available', 'corolla.png', 'Family Car'),
('Toyota Hiace',        '2022', 'Microbus',    4500.00, 'available', 'hiache.png', 'Group Travel Car'),
('Toyota Hilux',        '2023', 'PickUp',      3500.00, 'available', 'hilux.png', 'Pickup Truck'),
('Toyota Land Cruiser', '2023', 'SUV',         6000.00, 'available', 'landcruiser.png', 'Premium Car'),
('Toyota Premio',       '2021', 'Sedan',       2800.00, 'available', 'premio.png', 'Premium Business Car'),
('Toyota Noah',         '2022', 'Van',         3800.00, 'available', 'noah.png', 'Family Van');