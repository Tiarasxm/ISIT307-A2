<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Rental.php';

Auth::requireLogin();

$rental = new Rental();
$completedRentals = $rental->getCompletedRentalsByUser(Auth::getCurrentUserId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rental History - MotoCity</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            max-width: 1000px;
            padding: 2.5rem 2rem;
            margin-top: 4rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/nav.php'; ?>

    <main class="container">
        <h2>My Rental History</h2>
        
        <?php if (empty($completedRentals)): ?>
            <div class="message info">
                You have no completed rentals yet.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Motorbike Code</th>
                        <th>Description</th>
                        <th>Start Time</th>
                        <th>End Time</th>
                        <th>Cost/Hour</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($completedRentals as $r): ?>
                        <?php
                        $totalCost = $rental->calculateTotalCost(
                            $r['startDateTime'],
                            $r['endDateTime'],
                            $r['costPerHourAtStart']
                        );
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($r['motorbikeCode']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['description']); ?></td>
                            <td><?php echo $r['startDateTime']; ?></td>
                            <td><?php echo $r['endDateTime']; ?></td>
                            <td>$<?php echo number_format($r['costPerHourAtStart'], 2); ?></td>
                            <td><strong>$<?php echo number_format($totalCost, 2); ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p class="mt-1"><strong>Total Rentals:</strong> <?php echo count($completedRentals); ?></p>
        <?php endif; ?>
        
        <div class="mt-2">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </main>
</body>
</html>
