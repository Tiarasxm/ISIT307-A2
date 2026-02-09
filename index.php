<?php
/**
 * Index page - Landing/Welcome page
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';

$pageTitle = 'Welcome';
include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <div class="welcome-hero">
        <h2>Welcome to MotoCity</h2>
        <p style="font-size: 1.1rem; color: #666;">Motorbike rental management system</p>
    </div>
    
    <?php if (!Auth::isLoggedIn()): ?>
    <div class="text-center mt-2">
        <p style="margin-bottom: 1.5rem;">Please login or register to continue</p>
        <a href="login.php" class="btn">Login</a>
        <a href="register.php" class="btn btn-secondary">Register</a>
    </div>
    <?php else: ?>
    <div class="text-center mt-2">
        <p style="margin-bottom: 1.5rem;">Welcome back, <strong><?php echo Auth::getCurrentUserName(); ?></strong></p>
        <a href="dashboard.php" class="btn">Go to Dashboard</a>
    </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
