<?php
/**
 * Current Rentals page - Active rentals
 * User: Shows their own active rentals
 * Admin: Shows all active rentals
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Rental.php';

// Require login
Auth::requireLogin();

$pageTitle = 'Active Rentals';
$rental = new Rental();

// Get active rentals based on user type
if (Auth::isAdmin()) {
    $activeRentals = $rental->getAllActiveRentals();
    $listTitle = 'All Active Rentals';
} else {
    $activeRentals = $rental->getActiveRentalsByUser(Auth::getCurrentUserId());
    $listTitle = 'My Active Rentals';
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2><?php echo $listTitle; ?></h2>
    
    <?php if (empty($activeRentals)): ?>
        <div class="message info">
            <?php if (Auth::isAdmin()): ?>
                No active rentals at the moment.
            <?php else: ?>
                You don't have any active rentals.
            <?php endif; ?>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Rental ID</th>
                    <?php if (Auth::isAdmin()): ?>
                        <th>User</th>
                    <?php endif; ?>
                    <th>Motorbike Code</th>
                    <th>Description</th>
                    <th>Location</th>
                    <th>Start Date/Time</th>
                    <th>Cost/Hour</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeRentals as $r): ?>
                    <tr>
                        <td><strong>#<?php echo $r['rentalId']; ?></strong></td>
                        <?php if (Auth::isAdmin()): ?>
                            <td><?php echo htmlspecialchars($r['name'] . ' ' . $r['surname']); ?><br>
                                <small><?php echo htmlspecialchars($r['email']); ?></small>
                            </td>
                        <?php endif; ?>
                        <td><strong><?php echo htmlspecialchars($r['motorbikeCode']); ?></strong></td>
                        <td><?php echo htmlspecialchars($r['description']); ?></td>
                        <td><?php echo htmlspecialchars($r['rentingLocation']); ?></td>
                        <td><?php echo htmlspecialchars($r['startDateTime']); ?></td>
                        <td>$<?php echo number_format($r['costPerHourAtStart'], 2); ?></td>
                        <td><span class="badge badge-warning"><?php echo htmlspecialchars($r['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="mt-1"><strong>Total:</strong> <?php echo count($activeRentals); ?> active rental(s)</p>
    <?php endif; ?>
    
    <div class="mt-2">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <?php if (!empty($activeRentals)): ?>
            <a href="return.php" class="btn">Return Motorbike</a>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
