<?php
/**
 * Rental History page - Completed rentals
 * User: Shows their own completed rentals
 * Admin: Shows all completed rentals (optional)
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Rental.php';

// Require login
Auth::requireLogin();

$pageTitle = 'Rental History';
$rental = new Rental();

// Get completed rentals based on user type
if (Auth::isAdmin()) {
    $completedRentals = $rental->getAllCompletedRentals();
    $listTitle = 'All Completed Rentals';
} else {
    $completedRentals = $rental->getCompletedRentalsByUser(Auth::getCurrentUserId());
    $listTitle = 'My Rental History';
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2><?php echo $listTitle; ?></h2>
    
    <?php if (empty($completedRentals)): ?>
        <div class="message info">
            <?php if (Auth::isAdmin()): ?>
                No completed rentals yet.
            <?php else: ?>
                You don't have any completed rentals yet.
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
                    <th>End Date/Time</th>
                    <th>Cost/Hour</th>
                    <th>Total Cost</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($completedRentals as $r): ?>
                    <?php
                    // Calculate total cost
                    $totalCost = $rental->calculateTotalCost(
                        $r['startDateTime'],
                        $r['endDateTime'],
                        $r['costPerHourAtStart']
                    );
                    ?>
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
                        <td><?php echo htmlspecialchars($r['endDateTime']); ?></td>
                        <td>$<?php echo number_format($r['costPerHourAtStart'], 2); ?></td>
                        <td><strong>$<?php echo number_format($totalCost, 2); ?></strong></td>
                        <td><span class="badge badge-success"><?php echo htmlspecialchars($r['status']); ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="mt-1"><strong>Total:</strong> <?php echo count($completedRentals); ?> completed rental(s)</p>
    <?php endif; ?>
    
    <div class="mt-2">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
