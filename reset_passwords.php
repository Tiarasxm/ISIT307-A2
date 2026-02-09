<?php
/**
 * Reset all user passwords to 'password123'
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';

echo "<h2>Password Reset Tool</h2>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Generate new password hash
    $password = 'password123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    echo "<p>Resetting all passwords to: <strong>password123</strong></p>";
    echo "<p>New hash: <code>$hash</code></p>";
    echo "<hr>";
    
    // Update all users
    $stmt = $db->prepare("UPDATE users SET password = ?");
    $stmt->execute([$hash]);
    
    $affected = $stmt->rowCount();
    
    echo "<h3>✅ Success!</h3>";
    echo "<p>Updated <strong>$affected</strong> user account(s).</p>";
    
    // Show updated users
    $stmt = $db->query("SELECT id, name, email, type FROM users");
    $users = $stmt->fetchAll();
    
    echo "<h3>Updated Accounts:</h3>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Email</th><th>Password</th><th>Type</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td><strong>{$user['email']}</strong></td>";
        echo "<td>password123</td>";
        echo "<td>{$user['type']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    echo "<hr>";
    echo "<h3>Now you can login!</h3>";
    echo "<p><a href='login.php' style='background: #1abc9c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Go to Login Page</a></p>";
    
    echo "<hr>";
    echo "<h4>Test Accounts:</h4>";
    echo "<ul>";
    echo "<li><strong>Admin:</strong> admin@motocity.com / password123</li>";
    echo "<li><strong>User:</strong> john.smith@example.com / password123</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>
