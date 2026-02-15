<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Rental.php';

Auth::requireLogin();

$rental = new Rental();
$activeRentals = $rental->getActiveRentalsByUser(Auth::getCurrentUserId());
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Active Rentals - MotoCity</title>
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
        <h2>My Active Rentals</h2>
        
        <?php if (empty($activeRentals)): ?>
            <div class="message info">
                You have no active rentals at the moment.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Motorbike Code</th>
                        <th>Description</th>
                        <th>Location</th>
                        <th>Start Time</th>
                        <th>Cost/Hour</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activeRentals as $r): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($r['motorbikeCode']); ?></strong></td>
                            <td><?php echo htmlspecialchars($r['description']); ?></td>
                            <td><?php echo htmlspecialchars($r['rentingLocation']); ?></td>
                            <td><?php echo $r['startDateTime']; ?></td>
                            <td>$<?php echo number_format($r['costPerHourAtStart'], 2); ?></td>
                            <td>
                                <a href="return.php" class="btn btn-small">Return</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p class="mt-1"><strong>Total Active:</strong> <?php echo count($activeRentals); ?></p>
        <?php endif; ?>
        
        <div class="mt-2">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            <a href="return.php" class="btn">Return a Motorbike</a>
        </div>
    </main>
</body>
</html>
