<?php
/**
 * Auth class - Handles authentication and authorization
 * Session management and access control
 */
class Auth {
    
    /**
     * Check if user is logged in
     * @return bool
     */
    public static function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if current user is administrator
     * @return bool
     */
    public static function isAdmin() {
        return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'Administrator';
    }
    
    /**
     * Get current user ID
     * @return int|null
     */
    public static function getCurrentUserId() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Get current user type
     * @return string|null
     */
    public static function getCurrentUserType() {
        return isset($_SESSION['user_type']) ? $_SESSION['user_type'] : null;
    }
    
    /**
     * Get current user name
     * @return string|null
     */
    public static function getCurrentUserName() {
        return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
    }
    
    /**
     * Set user session after login
     * @param int $userId
     * @param string $name
     * @param string $surname
     * @param string $email
     * @param string $type
     */
    public static function setUserSession($userId, $name, $surname, $email, $type) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_surname'] = $surname;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_type'] = $type;
    }
    
    /**
     * Clear user session (logout)
     */
    public static function clearUserSession() {
        session_unset();
        session_destroy();
    }
    
    /**
     * Require user to be logged in (redirect to login if not)
     */
    public static function requireLogin() {
        if (!self::isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }
    
    /**
     * Require user to be administrator (redirect to dashboard if not)
     */
    public static function requireAdmin() {
        self::requireLogin();
        if (!self::isAdmin()) {
            $_SESSION['error_message'] = "Access denied. Administrator privileges required.";
            header("Location: dashboard.php");
            exit();
        }
    }
    
    /**
     * Redirect if already logged in
     * @param string $location Default redirect location
     */
    public static function redirectIfLoggedIn($location = 'dashboard.php') {
        if (self::isLoggedIn()) {
            header("Location: $location");
            exit();
        }
    }
}
?>
