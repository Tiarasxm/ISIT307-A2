<?php
/**
 * Rent Motorbike page
 * User: Rent for themselves
 * Admin: Rent for a particular user (select user + motorbike)
 */
require_once 'includes/config.php';
require_once 'includes/validation.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';
require_once 'classes/Motorbike.php';
require_once 'classes/Rental.php';

// Require login
Auth::requireLogin();

$pageTitle = 'Rent Motorbike';
$errors = [];
$rentalSuccess = false;
$rentalInfo = null;

$motorbike = new Motorbike();
$availableMotorbikes = $motorbike->getAvailableMotorbikes();

// Get users list for admin
$users = [];
if (Auth::isAdmin()) {
    $user = new User();
    $users = $user->getAllUsers();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $motorbikeCode = sanitizeString($_POST['motorbike_code'] ?? '');
    $startDateTime = sanitizeString($_POST['start_datetime'] ?? '');
    
    // For admin: get selected user, for regular user: use current user
    if (Auth::isAdmin()) {
        $userId = sanitizeString($_POST['user_id'] ?? '');
    } else {
        $userId = Auth::getCurrentUserId();
    }
    
    // Validate inputs
    $validations = [
        validateRequired($motorbikeCode, 'Motorbike'),
        validateDateTime($startDateTime, 'Start date/time')
    ];
    
    if (Auth::isAdmin()) {
        $validations[] = validateRequired($userId, 'User');
    }
    
    $errors = collectErrors($validations);
    
    // If no errors, create rental
    if (empty($errors)) {
        // Get motorbike details
        $bikeObj = new Motorbike();
        $bikeData = $bikeObj->getMotorbikeByCode($motorbikeCode);
        
        if (!$bikeData) {
            $errors[] = "Motorbike not found";
        } elseif (!$bikeObj->isAvailable($motorbikeCode)) {
            $errors[] = "Motorbike is not available for rent";
        } else {
            // Create rental
            $rental = new Rental();
            $rental->setUserId($userId);
            $rental->setMotorbikeCode($motorbikeCode);
            $rental->setStartDateTime($startDateTime);
            $rental->setCostPerHourAtStart($bikeData->getCostPerHour());
            
            $result = $rental->createRental();
            
            if ($result === true) {
                $rentalSuccess = true;
                $rentalInfo = [
                    'code' => $motorbikeCode,
                    'startDateTime' => $startDateTime,
                    'costPerHour' => $bikeData->getCostPerHour()
                ];
                
                // Refresh available motorbikes
                $availableMotorbikes = $motorbike->getAvailableMotorbikes();
            } else {
                $errors[] = $result;
            }
        }
    }
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2>Rent Motorbike</h2>
    
    <?php if ($rentalSuccess): ?>
        <!-- Rental Success Notification -->
        <div class="notification-box">
            <h3>✓ Rental Successful!</h3>
            <p><strong>Motorbike Code:</strong> <?php echo htmlspecialchars($rentalInfo['code']); ?></p>
            <p><strong>Start Date/Time:</strong> <?php echo htmlspecialchars($rentalInfo['startDateTime']); ?></p>
            <p><strong>Cost per Hour:</strong> $<?php echo number_format($rentalInfo['costPerHour'], 2); ?></p>
        </div>
        <div class="mt-1">
            <a href="rent.php" class="btn">Rent Another Motorbike</a>
            <a href="rentals_current.php" class="btn btn-secondary">View Active Rentals</a>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    <?php else: ?>
        
        <?php if (!empty($errors)): ?>
            <div class="message error">
                <strong>Please correct the following errors:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (empty($availableMotorbikes)): ?>
            <div class="message warning">
                No motorbikes are currently available for rent.
            </div>
            <div class="mt-1">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <form method="POST" action="rent.php">
                <?php if (Auth::isAdmin()): ?>
                    <div class="form-group">
                        <label for="user_id">Select User *</label>
                        <select id="user_id" name="user_id" required>
                            <option value="">-- Select User --</option>
                            <?php foreach ($users as $u): ?>
                                <option value="<?php echo $u['id']; ?>" <?php echo (isset($userId) && $userId == $u['id']) ? 'selected' : ''; ?>>
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
                            <option value="<?php echo htmlspecialchars($bike['code']); ?>" <?php echo (isset($motorbikeCode) && $motorbikeCode === $bike['code']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($bike['code'] . ' - ' . $bike['description'] . ' (' . $bike['rentingLocation'] . ') - $' . number_format($bike['costPerHour'], 2) . '/hr'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="start_datetime">Start Date/Time *</label>
                    <input type="datetime-local" id="start_datetime" name="start_datetime" 
                           value="<?php echo isset($startDateTime) ? htmlspecialchars($startDateTime) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Rent Motorbike</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            
            <script>
                // Set default datetime to current time
                document.addEventListener('DOMContentLoaded', function() {
                    var datetimeInput = document.getElementById('start_datetime');
                    if (!datetimeInput.value) {
                        var now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        datetimeInput.value = now.toISOString().slice(0, 16);
                    }
                });
            </script>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
