<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';
require_once 'classes/Rental.php';

Auth::requireLogin();

$isAdmin = Auth::isAdmin();
$motorbike = new Motorbike();
$rental = new Rental();

// Get statistics
if ($isAdmin) {
    $totalMotorbikes = count($motorbike->getAllMotorbikes());
    $availableMotorbikes = count($motorbike->getAvailableMotorbikes());
    $rentedMotorbikes = count($motorbike->getRentedMotorbikes());
    $activeRentals = count($rental->getAllActiveRentals());
} else {
    $availableMotorbikes = count($motorbike->getAvailableMotorbikes());
    $myActiveRentals = count($rental->getActiveRentalsByUser(Auth::getCurrentUserId()));
    $myCompletedRentals = count($rental->getCompletedRentalsByUser(Auth::getCurrentUserId()));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MotoCity</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            max-width: 1000px;
            padding: 2.5rem 2rem;
            margin-top: 4rem;
        }
        .card {
            padding: 1.5rem;
        }
        .card-grid {
            margin-top: 1.5rem;
            gap: 1.25rem;
        }
        h3 {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/nav.php'; ?>

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
        
        <?php if ($isAdmin): ?>
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
                
                <a href="rentals_current.php" class="card card-clickable">
                    <h3>My Active Rentals</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $myActiveRentals; ?></p>
                </a>
                
                <a href="rentals_history.php" class="card card-clickable">
                    <h3>Completed Rentals</h3>
                    <p style="font-size: 2rem; color: var(--color-accent); font-weight: bold;"><?php echo $myCompletedRentals; ?></p>
                </a>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
