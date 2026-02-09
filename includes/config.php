<?php
/**
 * Configuration file for MotoCity application
 * Contains database credentials and application settings
 */

// Database configuration
// For XAMPP on Mac, use 127.0.0.1 instead of localhost to avoid socket issues
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'motocity');
define('DB_USER', 'root');
define('DB_PASS', '');

// Application settings
define('SITE_NAME', 'MotoCity');
define('BASE_URL', 'http://localhost/ISIT307-A2');

// Password hashing method
// Set to 'password_hash' (recommended) or 'md5' (for lab compatibility)
define('HASH_METHOD', 'password_hash');

// Timezone
date_default_timezone_set('UTC');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
