<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Rental.php';
require_once 'classes/User.php';

Auth::requireLogin();

$isAdmin = Auth::isAdmin();
$errors = [];
$success = '';
$totalCost = 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rentalId = $_POST['rental_id'] ?? '';
    
    if (empty($rentalId)) {
        $errors[] = "Please select a rental to return";
    }
    
    if (empty($errors)) {
        $rental = new Rental();
        $rentalData = $rental->getRentalById($rentalId);
        
        if ($rentalData && $rentalData->getStatus() === 'ACTIVE') {
            $result = $rental->returnRental($rentalId);
            
            if ($result === true) {
                $totalCost = $rental->calculateTotalCost(
                    $rentalData->getStartDateTime(),
                    date('Y-m-d H:i:s'),
                    $rentalData->getCostPerHourAtStart()
                );
                
                $success = "Motorbike returned successfully!<br>";
                $success .= "<strong>Motorbike Code:</strong> " . htmlspecialchars($rentalData->getMotorbikeCode()) . "<br>";
                $success .= "<strong>Start Time:</strong> " . $rentalData->getStartDateTime() . "<br>";
                $success .= "<strong>End Time:</strong> " . date('Y-m-d H:i:s') . "<br>";
                $success .= "<strong>Total Cost:</strong> $" . number_format($totalCost, 2);
            } else {
                $errors[] = $result;
            }
        } else {
            $errors[] = "Rental not found or already returned";
        }
    }
}

// Get active rentals
$rental = new Rental();
if ($isAdmin) {
    $activeRentals = $rental->getAllActiveRentals();
} else {
    $activeRentals = $rental->getActiveRentalsByUser(Auth::getCurrentUserId());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Motorbike - MotoCity</title>
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
        <h2>Return a Motorbike</h2>
        
        <?php if ($success): ?>
            <div class="message success">
                <?php echo $success; ?>
            </div>
            <div class="mt-2">
                <a href="return.php" class="btn">Return Another</a>
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="message error">
                    <strong>Error:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <?php if (empty($activeRentals)): ?>
                <div class="message info">
                    No active rentals to return.
                </div>
            <?php else: ?>
                <h3>Active Rentals</h3>
                <form method="POST" action="return.php">
                    <table>
                        <thead>
                            <tr>
                                <th>Select</th>
                                <th>Motorbike Code</th>
                                <th>Description</th>
                                <?php if ($isAdmin): ?>
                                    <th>User</th>
                                <?php endif; ?>
                                <th>Start Time</th>
                                <th>Cost/Hour</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($activeRentals as $r): ?>
                                <tr>
                                    <td>
                                        <input type="radio" name="rental_id" value="<?php echo $r['rentalId']; ?>" required>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($r['motorbikeCode']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($r['description']); ?></td>
                                    <?php if ($isAdmin): ?>
                                        <td><?php echo htmlspecialchars($r['name'] . ' ' . $r['surname']); ?></td>
                                    <?php endif; ?>
                                    <td><?php echo $r['startDateTime']; ?></td>
                                    <td>$<?php echo number_format($r['costPerHourAtStart'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="form-group mt-2">
                        <button type="submit" class="btn">Return Selected Motorbike</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>
