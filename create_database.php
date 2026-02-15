<?php
// Create database and tables - tutorial style
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "motocity";

// Create database
$conn = new mysqli($servername, $username, $password);
if ($conn->connect_error) { die("Connection failed..."); }

$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) == TRUE) {
    echo "Database exists or created...<br>";
} else {
    echo "Error creating database...<br>";
}
$conn->close();

// Create tables
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { die("Connection failed..."); }

// Check and create users table
$checktable = $conn->query("SHOW TABLES LIKE 'users'");
if ($checktable->num_rows == 0) {
    $sql = "CREATE TABLE users (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        surname VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        type ENUM('Administrator', 'User') NOT NULL DEFAULT 'User',
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql) == TRUE) {
        echo "Users table created...<br>";
    } else {
        echo "Error creating users table...<br>";
    }
} else {
    echo "Users table exists.<br>";
}

// Check and create motorbikes table
$checktable = $conn->query("SHOW TABLES LIKE 'motorbikes'");
if ($checktable->num_rows == 0) {
    $sql = "CREATE TABLE motorbikes (
        code VARCHAR(50) PRIMARY KEY,
        rentingLocation VARCHAR(200) NOT NULL,
        description TEXT NOT NULL,
        costPerHour DECIMAL(10, 2) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql) == TRUE) {
        echo "Motorbikes table created...<br>";
    } else {
        echo "Error creating motorbikes table...<br>";
    }
} else {
    echo "Motorbikes table exists.<br>";
}

// Check and create rentals table
$checktable = $conn->query("SHOW TABLES LIKE 'rentals'");
if ($checktable->num_rows == 0) {
    $sql = "CREATE TABLE rentals (
        rentalId INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        userId INT(6) UNSIGNED NOT NULL,
        motorbikeCode VARCHAR(50) NOT NULL,
        startDateTime DATETIME NOT NULL,
        endDateTime DATETIME NULL,
        costPerHourAtStart DECIMAL(10, 2) NOT NULL,
        status ENUM('ACTIVE', 'COMPLETED') NOT NULL DEFAULT 'ACTIVE',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    if ($conn->query($sql) == TRUE) {
        echo "Rentals table created...<br>";
    } else {
        echo "Error creating rentals table...<br>";
    }
} else {
    echo "Rentals table exists.<br>";
}

// Insert sample data
$result = $conn->query("SELECT COUNT(*) as count FROM users");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    // Insert admin user (password: password123)
    $password_hash = password_hash('password123', PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, surname, phone, email, type, password) VALUES 
            ('Admin', 'Tan', '+6591234567', 'admin@motocity.com', 'Administrator', '$password_hash')";
    $conn->query($sql);
    
    // Insert sample users
    $sql = "INSERT INTO users (name, surname, phone, email, type, password) VALUES 
            ('Wei Ming', 'Lim', '+6591234568', 'weiming.lim@example.com', 'User', '$password_hash'),
            ('Siti', 'Rahman', '+6591234569', 'siti.rahman@example.com', 'User', '$password_hash'),
            ('Raj', 'Kumar', '+6591234570', 'raj.kumar@example.com', 'User', '$password_hash')";
    $conn->query($sql);
    echo "Sample users inserted...<br>";
}

// Insert sample motorbikes
$result = $conn->query("SELECT COUNT(*) as count FROM motorbikes");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $sql = "INSERT INTO motorbikes (code, rentingLocation, description, costPerHour) VALUES 
            ('MB001', 'Orchard MRT Station', 'Honda CBR500R - Sport bike, 500cc, Red color', 15.00),
            ('MB002', 'Changi Airport Terminal 3', 'Yamaha MT-07 - Naked bike, 689cc, Blue color', 18.00),
            ('MB003', 'Marina Bay Sands', 'Kawasaki Ninja 400 - Sport bike, 399cc, Green color', 12.00),
            ('MB004', 'East Coast Park', 'Suzuki V-Strom 650 - Adventure bike, 645cc, White color', 20.00),
            ('MB005', 'NUS Campus', 'Honda PCX 150 - Scooter, 150cc, Black color', 8.00)";
    $conn->query($sql);
    echo "Sample motorbikes inserted...<br>";
}

$conn->close();
echo "<br><a href='index.php'>Go to Home Page</a>";
?>
