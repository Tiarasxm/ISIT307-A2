<?php
/**
 * Quick diagnostic and fix for login issues
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';

echo "<h2>Login Issue Diagnostic & Fix</h2>";
echo "<hr>";

try {
    $db = Database::getInstance()->getConnection();
    
    // Step 1: Check if users exist
    echo "<h3>Step 1: Checking Users</h3>";
    $stmt = $db->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "Users in database: <strong>{$result['count']}</strong><br>";
    
    if ($result['count'] == 0) {
        echo "<p style='color: red;'>❌ No users found! Database needs to be imported.</p>";
        echo "<p><a href='http://localhost/phpmyadmin'>Go to phpMyAdmin to import schema.sql</a></p>";
        exit;
    }
    
    // Step 2: Show current users
    echo "<h3>Step 2: Current Users</h3>";
    $stmt = $db->query("SELECT id, name, email, type, LEFT(password, 10) as pass_preview FROM users");
    $users = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='8'>";
    echo "<tr><th>Email</th><th>Type</th><th>Password Preview</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['type']}</td>";
        echo "<td><code>{$user['pass_preview']}...</code></td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Step 3: Test password hashing
    echo "<h3>Step 3: Password Hash Method</h3>";
    echo "Current HASH_METHOD: <strong>" . HASH_METHOD . "</strong><br>";
    
    $testPass = 'password123';
    $newHash = password_hash($testPass, PASSWORD_DEFAULT);
    echo "Test hash for 'password123': <code>" . substr($newHash, 0, 30) . "...</code><br>";
    echo "Hash starts with: <strong>" . substr($newHash, 0, 4) . "</strong><br>";
    
    // Step 4: Check admin user's current hash
    echo "<h3>Step 4: Admin Account Check</h3>";
    $stmt = $db->query("SELECT email, password FROM users WHERE email = 'admin@motocity.com'");
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "Admin email: <strong>{$admin['email']}</strong><br>";
        echo "Current hash starts with: <strong>" . substr($admin['password'], 0, 4) . "</strong><br>";
        
        // Test if current password works
        $testResult = password_verify('password123', $admin['password']);
        echo "Password verify test: " . ($testResult ? "✅ WORKS" : "❌ FAILS") . "<br>";
        
        if (!$testResult) {
            echo "<p style='background: #fff3cd; padding: 15px; border-left: 4px solid #ffc107;'>";
            echo "⚠️ <strong>Password hash is incorrect!</strong><br>";
            echo "Click the button below to fix all passwords.";
            echo "</p>";
        }
    }
    
    echo "<hr>";
    echo "<h3>🔧 Fix All Passwords</h3>";
    echo "<form method='POST' action='quick_fix.php'>";
    echo "<button type='submit' name='fix_passwords' style='background: #e74c3c; color: white; padding: 15px 30px; border: none; border-radius: 4px; font-size: 16px; cursor: pointer;'>Reset All Passwords to 'password123'</button>";
    echo "</form>";
    
    // Handle password reset
    if (isset($_POST['fix_passwords'])) {
        echo "<hr>";
        echo "<h3>🔄 Resetting Passwords...</h3>";
        
        $newPassword = 'password123';
        $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("UPDATE users SET password = ?");
        $stmt->execute([$newHash]);
        
        echo "<div style='background: #d4edda; padding: 20px; border-left: 4px solid #28a745; margin: 20px 0;'>";
        echo "<h3 style='margin-top: 0;'>✅ Success!</h3>";
        echo "<p>All passwords have been reset to: <strong>password123</strong></p>";
        echo "<p>You can now login with these accounts:</p>";
        echo "<ul>";
        echo "<li><strong>Admin:</strong> admin@motocity.com / password123</li>";
        echo "<li><strong>User:</strong> john.smith@example.com / password123</li>";
        echo "</ul>";
        echo "<p><a href='login.php' style='background: #1abc9c; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;'>Go to Login Page →</a></p>";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; padding: 20px; border-left: 4px solid #dc3545;'>";
    echo "<h3>❌ Error</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "</div>";
}
?>
