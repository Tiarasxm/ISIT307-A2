<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MotoCity - Motorbike Rental</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        main.container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex: 1;
            padding: 2rem;
        }
        main.container .card {
            max-width: 600px;
            width: 100%;
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
            <h1>MotoCity</h1>
            <div class="header-buttons">
                <?php if (!isset($_SESSION['user_id'])): ?>
                    <a href="login.php" class="btn-login">Login</a>
                    <a href="register.php" class="btn-register">Register</a>
                <?php else: ?>
                    <a href="dashboard.php" class="btn-dashboard">Dashboard</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="container">
        <div class="card">
            <h2>Welcome to MotoCity</h2>
            <p>Your trusted motorbike rental service in Singapore.</p>
            
            <?php if (!isset($_SESSION['user_id'])): ?>
                <p>Please login or register to start renting motorbikes.</p>
                <div style="margin-top: 1.5rem;">
                    <a href="login.php" class="btn">Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </div>
            <?php else: ?>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
                <div style="margin-top: 1.5rem;">
                    <a href="dashboard.php" class="btn">Go to Dashboard</a>
                </div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
