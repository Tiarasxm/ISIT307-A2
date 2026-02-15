<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';
require_once 'classes/Rental.php';
require_once 'classes/User.php';

Auth::requireLogin();

$isAdmin = Auth::isAdmin();
$errors = [];
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motorbikeCode = $_POST['motorbike_code'] ?? '';
    $userId = $isAdmin ? ($_POST['user_id'] ?? '') : Auth::getCurrentUserId();
    
    if (empty($motorbikeCode)) {
        $errors[] = "Please select a motorbike";
    }
    if ($isAdmin && empty($userId)) {
        $errors[] = "Please select a user";
    }
    
    if (empty($errors)) {
        $motorbike = new Motorbike();
        $motoData = $motorbike->getByCode($motorbikeCode);
        
        if ($motoData) {
            $rental = new Rental();
            $rental->setUserId($userId);
            $rental->setMotorbikeCode($motorbikeCode);
            $rental->setStartDateTime(date('Y-m-d H:i:s'));
            $rental->setCostPerHourAtStart($motoData->getCostPerHour());
            
            $result = $rental->createRental();
            
            if ($result === true) {
                $success = "Rental started successfully!<br>";
                $success .= "<strong>Motorbike:</strong> " . htmlspecialchars($motoData->getCode()) . "<br>";
                $success .= "<strong>Start Time:</strong> " . date('Y-m-d H:i:s') . "<br>";
                $success .= "<strong>Cost per Hour:</strong> $" . number_format($motoData->getCostPerHour(), 2);
            } else {
                $errors[] = $result;
            }
        } else {
            $errors[] = "Motorbike not found";
        }
    }
}

// Get available motorbikes
$motorbike = new Motorbike();
$availableMotorbikes = $motorbike->getAvailableMotorbikes();

// Get all users if admin
$users = [];
if ($isAdmin) {
    $user = new User();
    $users = $user->getAllUsers();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rent Motorbike - MotoCity</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            max-width: 800px;
            padding: 2.5rem 2rem;
            margin-top: 4rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/nav.php'; ?>

    <main class="container">
        <h2>Rent a Motorbike</h2>
        
        <?php if ($success): ?>
            <div class="message success">
                <?php echo $success; ?>
            </div>
            <div class="mt-2">
                <a href="rent.php" class="btn">Rent Another</a>
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
            
            <?php if (empty($availableMotorbikes)): ?>
                <div class="message info">
                    No motorbikes available for rent at the moment.
                </div>
            <?php else: ?>
                <form method="POST" action="rent.php">
                    <?php if ($isAdmin): ?>
                        <div class="form-group">
                            <label for="user_id">Select User *</label>
                            <select id="user_id" name="user_id" required>
                                <option value="">-- Select User --</option>
                                <?php foreach ($users as $u): ?>
                                    <option value="<?php echo $u['id']; ?>">
                                        <?php echo htmlspecialchars($u['name'] . ' ' . $u['surname'] . ' (' . $u['email'] . ')'); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="motorbike_code">Select Motorbike *</label>
                        <select id="motorbike_code" name="motorbike_code" required>
                            <option value="">-- Select Motorbike --</option>
                            <?php foreach ($availableMotorbikes as $bike): ?>
                                <option value="<?php echo htmlspecialchars($bike['code']); ?>">
                                    <?php echo htmlspecialchars($bike['code'] . ' - ' . $bike['description'] . ' - $' . number_format($bike['costPerHour'], 2) . '/hr'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Start Rental</button>
                        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            <?php endif; ?>
        <?php endif; ?>
    </main>
</body>
</html>
