<?php
/**
 * Database Connection Test Script
 * This script tests the database connection and displays diagnostic information
 */

echo "<h2>Database Connection Test</h2>";

// Test 1: Check if config file exists
echo "<h3>Test 1: Configuration File</h3>";
if (file_exists('includes/config.php')) {
    echo "✓ Config file exists<br>";
    require_once 'includes/config.php';
    echo "✓ Config file loaded successfully<br>";
    echo "Database Host: " . DB_HOST . "<br>";
    echo "Database Name: " . DB_NAME . "<br>";
    echo "Database User: " . DB_USER . "<br>";
} else {
    echo "✗ Config file not found<br>";
    exit;
}

// Test 2: Check if Database class exists
echo "<h3>Test 2: Database Class</h3>";
if (file_exists('classes/Database.php')) {
    echo "✓ Database class file exists<br>";
    require_once 'classes/Database.php';
    echo "✓ Database class loaded successfully<br>";
} else {
    echo "✗ Database class file not found<br>";
    exit;
}

// Test 3: Test connection using unix socket (XAMPP Mac)
echo "<h3>Test 3: Connection Test (Unix Socket)</h3>";
try {
    $dsn = "mysql:unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock;dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    echo "✓ Successfully connected using unix socket<br>";
    echo "✓ Connection method: Unix Socket<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✓ Query test successful - Found " . $result['count'] . " users<br>";
    
} catch (PDOException $e) {
    echo "✗ Unix socket connection failed: " . $e->getMessage() . "<br>";
}

// Test 4: Test connection using host:port (alternative)
echo "<h3>Test 4: Connection Test (Host:Port)</h3>";
try {
    $dsn = "mysql:host=" . DB_HOST . ";port=3306;dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo2 = new PDO($dsn, DB_USER, DB_PASS);
    echo "✓ Successfully connected using host:port<br>";
    echo "✓ Connection method: Host:Port (127.0.0.1:3306)<br>";
    
    // Test query
    $stmt = $pdo2->query("SELECT COUNT(*) as count FROM motorbikes");
    $result = $stmt->fetch();
    echo "✓ Query test successful - Found " . $result['count'] . " motorbikes<br>";
    
} catch (PDOException $e) {
    echo "✗ Host:port connection failed: " . $e->getMessage() . "<br>";
}

// Test 5: Test Database singleton class
echo "<h3>Test 5: Database Singleton Class</h3>";
try {
    $db = Database::getInstance();
    echo "✓ Database singleton instance created<br>";
    
    $conn = $db->getConnection();
    echo "✓ PDO connection retrieved<br>";
    
    $stmt = $conn->query("SELECT COUNT(*) as count FROM rentals");
    $result = $stmt->fetch();
    echo "✓ Query test successful - Found " . $result['count'] . " rentals<br>";
    
} catch (Exception $e) {
    echo "✗ Database singleton test failed: " . $e->getMessage() . "<br>";
}

// Test 6: Check MySQL socket file
echo "<h3>Test 6: MySQL Socket File</h3>";
$socket_path = "/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";
if (file_exists($socket_path)) {
    echo "✓ MySQL socket file exists at: $socket_path<br>";
} else {
    echo "✗ MySQL socket file NOT found at: $socket_path<br>";
    echo "This might be why the connection is failing.<br>";
}

echo "<h3>Summary</h3>";
echo "If all tests passed, your database connection is working correctly!<br>";
echo "If any tests failed, check the error messages above for details.<br>";
?>
