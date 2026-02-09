<?php
/**
 * Database Connection Test Script
 * This will help diagnose connection issues
 */

echo "<h2>MotoCity Database Connection Test</h2>";
echo "<hr>";

// Test 1: Check if MySQL extension is loaded
echo "<h3>Test 1: PHP MySQL Extensions</h3>";
if (extension_loaded('pdo_mysql')) {
    echo "✅ PDO MySQL extension is loaded<br>";
} else {
    echo "❌ PDO MySQL extension is NOT loaded<br>";
}

// Test 2: Try to connect to MySQL
echo "<h3>Test 2: MySQL Connection</h3>";
$user = 'root';
$pass = '';

try {
    // Use XAMPP socket path
    $pdo = new PDO("mysql:unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock", $user, $pass);
    echo "✅ Successfully connected to MySQL server<br>";
    
    // Test 3: Check if database exists
    echo "<h3>Test 3: Database 'motocity' Check</h3>";
    $stmt = $pdo->query("SHOW DATABASES LIKE 'motocity'");
    $dbExists = $stmt->fetch();
    
    if ($dbExists) {
        echo "✅ Database 'motocity' exists<br>";
        
        // Test 4: Check tables
        echo "<h3>Test 4: Tables Check</h3>";
        $pdo->exec("USE motocity");
        $stmt = $pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        if (count($tables) > 0) {
            echo "✅ Found " . count($tables) . " tables:<br>";
            echo "<ul>";
            foreach ($tables as $table) {
                echo "<li>$table</li>";
            }
            echo "</ul>";
            
            // Test 5: Check if there are users
            echo "<h3>Test 5: Sample Data Check</h3>";
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch();
            echo "✅ Users table has {$result['count']} records<br>";
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM motorbikes");
            $result = $stmt->fetch();
            echo "✅ Motorbikes table has {$result['count']} records<br>";
            
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM rentals");
            $result = $stmt->fetch();
            echo "✅ Rentals table has {$result['count']} records<br>";
            
        } else {
            echo "❌ No tables found. Database needs to be imported!<br>";
            echo "<strong>Action Required:</strong> Import schema.sql<br>";
        }
        
    } else {
        echo "❌ Database 'motocity' does NOT exist<br>";
        echo "<strong>Action Required:</strong> Import schema.sql to create database<br>";
    }
    
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "<br>";
    echo "<br><strong>Possible Issues:</strong><br>";
    echo "<ul>";
    echo "<li>MySQL is not running in XAMPP</li>";
    echo "<li>MySQL port is not 3306 (default)</li>";
    echo "<li>MySQL root password is set (try updating DB_PASS in config.php)</li>";
    echo "</ul>";
    echo "<br><strong>Solutions:</strong><br>";
    echo "<ol>";
    echo "<li>Open XAMPP Control Panel</li>";
    echo "<li>Click 'Start' for MySQL</li>";
    echo "<li>Wait for MySQL to start (should show 'Running')</li>";
    echo "<li>Refresh this page</li>";
    echo "</ol>";
}

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If MySQL is not running: Start it in XAMPP Control Panel</li>";
echo "<li>If database doesn't exist: Import schema.sql via phpMyAdmin</li>";
echo "<li>Once all tests pass: <a href='index.php'>Go to MotoCity Application</a></li>";
echo "</ol>";
?>
