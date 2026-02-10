-- MotoCity Database Schema
-- Motorbike Rental Management System
-- Created for ISIT307 Assignment

-- Create database
CREATE DATABASE IF NOT EXISTS motocity;
USE motocity;

-- Drop tables if they exist (for clean reinstall)
DROP TABLE IF EXISTS rentals;
DROP TABLE IF EXISTS motorbikes;
DROP TABLE IF EXISTS users;

-- ============================================
-- Table: users
-- Stores user information (both Administrator and User types)
-- ============================================
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    type ENUM('Administrator', 'User') NOT NULL DEFAULT 'User',
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: motorbikes
-- Stores motorbike information
-- ============================================
CREATE TABLE motorbikes (
    code VARCHAR(50) PRIMARY KEY,
    rentingLocation VARCHAR(200) NOT NULL,
    description TEXT NOT NULL,
    costPerHour DECIMAL(10, 2) NOT NULL CHECK (costPerHour > 0),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_location (rentingLocation)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- Table: rentals
-- Stores rental transactions
-- ============================================
CREATE TABLE rentals (
    rentalId INT AUTO_INCREMENT PRIMARY KEY,
    userId INT NOT NULL,
    motorbikeCode VARCHAR(50) NOT NULL,
    startDateTime DATETIME NOT NULL,
    endDateTime DATETIME NULL,
    costPerHourAtStart DECIMAL(10, 2) NOT NULL,
    status ENUM('ACTIVE', 'COMPLETED') NOT NULL DEFAULT 'ACTIVE',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userId) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (motorbikeCode) REFERENCES motorbikes(code) ON DELETE CASCADE,
    INDEX idx_user (userId),
    INDEX idx_motorbike (motorbikeCode),
    INDEX idx_status (status),
    INDEX idx_start_date (startDateTime)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- SEED DATA
-- ============================================

-- Insert Users
-- Note: Passwords are hashed using password_hash() with PASSWORD_DEFAULT
-- Default passwords for all accounts: "password123"
-- Hash generated: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi

INSERT INTO users (name, surname, phone, email, type, password) VALUES
-- Administrator account
('Admin', 'Tan', '+6591234567', 'admin@motocity.com', 'Administrator', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),

-- Regular user accounts
('Wei Ming', 'Lim', '+6591234568', 'weiming.lim@example.com', 'User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Siti', 'Rahman', '+6591234569', 'siti.rahman@example.com', 'User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Raj', 'Kumar', '+6591234570', 'raj.kumar@example.com', 'User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('Mei Ling', 'Wong', '+6591234571', 'meiling.wong@example.com', 'User', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert Motorbikes
INSERT INTO motorbikes (code, rentingLocation, description, costPerHour) VALUES
('MB001', 'Orchard MRT Station', 'Honda CBR500R - Sport bike, 500cc, Red color, excellent condition', 15.00),
('MB002', 'Changi Airport Terminal 3', 'Yamaha MT-07 - Naked bike, 689cc, Blue color, comfortable for long rides', 18.00),
('MB003', 'Marina Bay Sands', 'Kawasaki Ninja 400 - Sport bike, 399cc, Green color, perfect for beginners', 12.00),
('MB004', 'East Coast Park', 'Suzuki V-Strom 650 - Adventure bike, 645cc, White color, great for touring', 20.00),
('MB005', 'NUS Campus', 'Honda PCX 150 - Scooter, 150cc, Black color, fuel efficient and easy to ride', 8.00),
('MB006', 'Raffles Place MRT', 'Ducati Monster 821 - Naked bike, 821cc, Red color, high performance', 25.00),
('MB007', 'VivoCity Shopping Centre', 'BMW G 310 R - Naked bike, 313cc, White color, premium quality', 16.00),
('MB008', 'Jurong East MRT', 'KTM Duke 390 - Naked bike, 373cc, Orange color, sporty and agile', 14.00);

-- Insert Sample Rentals (some active, some completed)
INSERT INTO rentals (userId, motorbikeCode, startDateTime, endDateTime, costPerHourAtStart, status) VALUES
-- Completed rentals
(2, 'MB001', '2024-01-15 09:00:00', '2024-01-15 14:00:00', 15.00, 'COMPLETED'),
(3, 'MB002', '2024-01-16 10:00:00', '2024-01-16 16:00:00', 18.00, 'COMPLETED'),
(2, 'MB003', '2024-01-17 08:00:00', '2024-01-17 12:00:00', 12.00, 'COMPLETED'),
(4, 'MB005', '2024-01-18 11:00:00', '2024-01-18 15:00:00', 8.00, 'COMPLETED'),
(5, 'MB004', '2024-01-19 07:00:00', '2024-01-19 19:00:00', 20.00, 'COMPLETED'),

-- Active rentals (currently rented)
(2, 'MB002', '2024-02-01 09:00:00', NULL, 18.00, 'ACTIVE'),
(3, 'MB006', '2024-02-01 10:00:00', NULL, 25.00, 'ACTIVE');

-- ============================================
-- VERIFICATION QUERIES (for testing)
-- ============================================

-- Uncomment these to verify the data after import:

-- SELECT * FROM users;
-- SELECT * FROM motorbikes;
-- SELECT * FROM rentals;

-- Check available motorbikes:
-- SELECT m.* FROM motorbikes m
-- WHERE m.code NOT IN (SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE');

-- Check currently rented motorbikes:
-- SELECT DISTINCT m.* FROM motorbikes m
-- INNER JOIN rentals r ON m.code = r.motorbikeCode
-- WHERE r.status = 'ACTIVE';

-- Check users currently renting:
-- SELECT DISTINCT u.* FROM users u
-- INNER JOIN rentals r ON u.id = r.userId
-- WHERE r.status = 'ACTIVE';

-- ============================================
-- END OF SCHEMA
-- ============================================
