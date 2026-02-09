<?php
/**
 * Dashboard page - Role-based landing page after login
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';
require_once 'classes/Rental.php';

// Require login
Auth::requireLogin();

$pageTitle = 'Dashboard';

// Get statistics
$motorbike = new Motorbike();
$rental = new Rental();

if (Auth::isAdmin()) {
    // Admin statistics
    $totalMotorbikes = count($motorbike->getAllMotorbikes());
    $availableMotorbikes = count($motorbike->getAvailableMotorbikes());
    $rentedMotorbikes = count($motorbike->getRentedMotorbikes());
    $activeRentals = count($rental->getAllActiveRentals());
} else {
    // User statistics
    $availableMotorbikes = count($motorbike->getAvailableMotorbikes());
    $myActiveRentals = count($rental->getActiveRentalsByUser(Auth::getCurrentUserId()));
    $myCompletedRentals = count($rental->getCompletedRentalsByUser(Auth::getCurrentUserId()));
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2>Dashboard</h2>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="message error">
            <?php 
            echo htmlspecialchars($_SESSION['error_message']); 
            unset($_SESSION['error_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="message success">
            <?php 
            echo htmlspecialchars($_SESSION['success_message']); 
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <div class="card">
        <h3>Welcome, <?php echo htmlspecialchars(Auth::getCurrentUserName() . ' ' . $_SESSION['user_surname']); ?>!</h3>
        <p><strong>Account Type:</strong> <span class="badge badge-info"><?php echo htmlspecialchars(Auth::getCurrentUserType()); ?></span></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
    </div>
    
    <?php if (Auth::isAdmin()): ?>
        <!-- Administrator Dashboard -->
        <h3>System Overview</h3>
        <div class="card-grid">
            <div class="card">
                <h3>Total Motorbikes</h3>
                <p style="font-size: 2rem; color: #1abc9c; font-weight: bold;"><?php echo $totalMotorbikes; ?></p>
                <a href="motorbikes_list.php" class="btn btn-small">View All</a>
            </div>
            
            <div class="card">
                <h3>Available Motorbikes</h3>
                <p style="font-size: 2rem; color: #27ae60; font-weight: bold;"><?php echo $availableMotorbikes; ?></p>
                <a href="motorbikes_list.php?filter=available" class="btn btn-small">View Available</a>
            </div>
            
            <div class="card">
                <h3>Rented Motorbikes</h3>
                <p style="font-size: 2rem; color: #e67e22; font-weight: bold;"><?php echo $rentedMotorbikes; ?></p>
                <a href="motorbikes_list.php?filter=rented" class="btn btn-small">View Rented</a>
            </div>
            
            <div class="card">
                <h3>Active Rentals</h3>
                <p style="font-size: 2rem; color: #3498db; font-weight: bold;"><?php echo $activeRentals; ?></p>
                <a href="rentals_current.php" class="btn btn-small">View Rentals</a>
            </div>
        </div>
        
        <h3>Quick Actions</h3>
        <div class="mt-1">
            <a href="motorbike_form.php" class="btn">Add New Motorbike</a>
            <a href="rent.php" class="btn btn-secondary">Rent Motorbike for User</a>
            <a href="return.php" class="btn btn-secondary">Return Motorbike</a>
            <a href="users_list.php" class="btn btn-secondary">Manage Users</a>
        </div>
        
    <?php else: ?>
        <!-- User Dashboard -->
        <h3>My Rental Overview</h3>
        <div class="card-grid">
            <div class="card">
                <h3>Available Motorbikes</h3>
                <p style="font-size: 2rem; color: #27ae60; font-weight: bold;"><?php echo $availableMotorbikes; ?></p>
                <a href="motorbikes_list.php" class="btn btn-small">Browse Motorbikes</a>
            </div>
            
            <div class="card">
                <h3>My Active Rentals</h3>
                <p style="font-size: 2rem; color: #e67e22; font-weight: bold;"><?php echo $myActiveRentals; ?></p>
                <a href="rentals_current.php" class="btn btn-small">View Active</a>
            </div>
            
            <div class="card">
                <h3>Completed Rentals</h3>
                <p style="font-size: 2rem; color: #3498db; font-weight: bold;"><?php echo $myCompletedRentals; ?></p>
                <a href="rentals_history.php" class="btn btn-small">View History</a>
            </div>
        </div>
        
        <h3>Quick Actions</h3>
        <div class="mt-1">
            <a href="motorbikes_list.php" class="btn">Browse Available Motorbikes</a>
            <a href="rent.php" class="btn btn-secondary">Rent a Motorbike</a>
            <?php if ($myActiveRentals > 0): ?>
                <a href="return.php" class="btn btn-secondary">Return a Motorbike</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
