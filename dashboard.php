<?php
session_start();

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$user_id = $_SESSION['user_id'];
$user_type = $_SESSION['user_type'];
$is_admin = ($user_type === 'Administrator');

// Get statistics
if ($is_admin) {
    // Admin statistics
    $result = $conn->query("SELECT COUNT(*) as count FROM motorbikes");
    $totalMotorbikes = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM motorbikes WHERE code NOT IN (SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE')");
    $availableMotorbikes = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(DISTINCT motorbikeCode) as count FROM rentals WHERE status = 'ACTIVE'");
    $rentedMotorbikes = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE status = 'ACTIVE'");
    $activeRentals = $result->fetch_assoc()['count'];
} else {
    // User statistics
    $result = $conn->query("SELECT COUNT(*) as count FROM motorbikes WHERE code NOT IN (SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE')");
    $availableMotorbikes = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE userId = $user_id AND status = 'ACTIVE'");
    $myActiveRentals = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM rentals WHERE userId = $user_id AND status = 'COMPLETED'");
    $myCompletedRentals = $result->fetch_assoc()['count'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MotoCity</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>MotoCity</h1>
        </div>
    </header>
    
    <nav>
        <div class="container">
            <ul>
                <li><a href="dashboard.php" class="active">Dashboard</a></li>
                <li><a href="motorbikes_list.php">Motorbikes</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <h2>Dashboard</h2>
        
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success">
                <?php 
                echo htmlspecialchars($_SESSION['success_message']); 
                unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        
        <div class="card">
            <h3>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] . ' ' . $_SESSION['user_surname']); ?>!</h3>
            <p><strong>Account Type:</strong> <span class="badge badge-info"><?php echo htmlspecialchars($_SESSION['user_type']); ?></span></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        </div>
        
        <?php if ($is_admin): ?>
            <!-- Administrator Dashboard -->
            <h3>System Overview</h3>
            <div class="card-grid">
                <a href="motorbikes_list.php" class="card card-clickable">
                    <h3>Total Motorbikes</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $totalMotorbikes; ?></p>
                </a>
                
                <a href="motorbikes_list.php?filter=available" class="card card-clickable">
                    <h3>Available Motorbikes</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $availableMotorbikes; ?></p>
                </a>
                
                <a href="motorbikes_list.php?filter=rented" class="card card-clickable">
                    <h3>Rented Motorbikes</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $rentedMotorbikes; ?></p>
                </a>
                
                <div class="card">
                    <h3>Active Rentals</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $activeRentals; ?></p>
                </div>
            </div>
            
        <?php else: ?>
            <!-- User Dashboard -->
            <h3>My Rental Overview</h3>
            <div class="card-grid">
                <a href="motorbikes_list.php" class="card card-clickable">
                    <h3>Available Motorbikes</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $availableMotorbikes; ?></p>
                </a>
                
                <div class="card">
                    <h3>My Active Rentals</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $myActiveRentals; ?></p>
                </div>
                
                <div class="card">
                    <h3>Completed Rentals</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $myCompletedRentals; ?></p>
                </div>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
