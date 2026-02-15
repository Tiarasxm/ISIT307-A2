<?php
/**
 * Auth class - Helper for authentication
 */
class Auth {
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    public static function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Administrator';
    }
    
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }
    
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            header("Location: dashboard.php");
            exit();
        }
    }
    
    public static function redirectIfLoggedIn() {
        if (self::isLoggedIn()) {
            header("Location: dashboard.php");
            exit();
        }
    }
    
    public static function setUserSession($id, $name, $surname, $email, $type) {
        $_SESSION['user_id'] = $id;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_surname'] = $surname;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_type'] = $type;
    }
    
    public static function getCurrentUserId() {
        return $_SESSION['user_id'] ?? null;
    }
    
    public static function getCurrentUserName() {
        return $_SESSION['user_name'] ?? '';
    }
}
?>
