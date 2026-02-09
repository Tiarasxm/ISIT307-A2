<?php
/**
 * Check users and password hashes
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';

echo "<h2>User Accounts Check</h2>";

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT id, name, email, type, password FROM users");
    $users = $stmt->fetchAll();
    
    echo "<h3>Found " . count($users) . " users:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Password Hash</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['name']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['type']}</td>";
        echo "<td>" . substr($user['password'], 0, 30) . "...</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Password Hash Test</h3>";
    $testPassword = 'password123';
    $hash = password_hash($testPassword, PASSWORD_DEFAULT);
    echo "Test password: <strong>password123</strong><br>";
    echo "Generated hash: <code>$hash</code><br>";
    echo "Verify test: " . (password_verify($testPassword, $hash) ? "✅ PASS" : "❌ FAIL") . "<br>";
    
    echo "<hr>";
    echo "<h3>Fix Required?</h3>";
    echo "<p>If password hashes don't start with <code>\$2y\$</code>, they need to be regenerated.</p>";
    echo "<p><a href='reset_passwords.php' style='background: #e74c3c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Click Here to Reset All Passwords</a></p>";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
