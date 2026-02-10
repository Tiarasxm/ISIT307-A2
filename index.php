<?php
/**
 * Index page - Landing/Welcome page
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';

$pageTitle = 'Welcome';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            margin-top: 4rem !important;
        }
        .index-header {
            background-color: #2C3E50;
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .index-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .index-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .header-buttons {
            display: flex;
            gap: 1rem;
        }
        .header-buttons a {
            padding: 0.6rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .header-buttons .btn-login {
            background-color: transparent;
            color: white;
            border: 1px solid white;
        }
        .header-buttons .btn-login:hover {
            background-color: white;
            color: #2C3E50;
        }
        .header-buttons .btn-register {
            background-color: #FF9A6C;
            color: white;
            border: 1px solid #FF9A6C;
        }
        .header-buttons .btn-register:hover {
            background-color: #e8855a;
            border-color: #e8855a;
        }
        .header-buttons .btn-dashboard {
            background-color: #FF9A6C;
            color: white;
            border: 1px solid #FF9A6C;
        }
        .header-buttons .btn-dashboard:hover {
            background-color: #e8855a;
        }
    </style>
</head>
<body>
    <header class="index-header">
        <div class="container">
            <h1><?php echo SITE_NAME; ?></h1>
            <div class="header-buttons">
                <?php if (!Auth::isLoggedIn()): ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                <?php else: ?>
                    <span style="margin-right: 1rem;">Welcome, <strong><?php echo Auth::getCurrentUserName(); ?></strong></span>
                    <a href="dashboard.php" class="btn-dashboard">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

<main class="container">
    <div class="welcome-hero">
        <h2>Welcome to MotoCity</h2>
        <p style="font-size: 1.1rem; color: #666;">Motorbike Rental Management System</p>
    </div>
    
    <?php if (!Auth::isLoggedIn()): ?>
    <div class="text-center mt-2">
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
