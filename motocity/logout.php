<?php
/**
 * Logout page - Clear user session
 */
require_once 'includes/config.php';
require_once 'classes/Auth.php';

// Clear session
Auth::clearUserSession();

// Redirect to home page
header("Location: index.php");
exit();
?>
