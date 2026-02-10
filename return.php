<?php
/**
 * Return Motorbike page
 * User: Return their own rented motorbikes
 * Admin: Return any user's rented motorbike
 */
require_once 'includes/config.php';
require_once 'includes/validation.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Rental.php';

// Require login
Auth::requireLogin();

$pageTitle = 'Return Motorbike';
$errors = [];
$returnSuccess = false;
$returnInfo = null;

$rental = new Rental();

// Get active rentals based on user type
if (Auth::isAdmin()) {
    $activeRentals = $rental->getAllActiveRentals();
} else {
    $activeRentals = $rental->getActiveRentalsByUser(Auth::getCurrentUserId());
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rentalId = sanitizeString($_POST['rental_id'] ?? '');
    $endDateTime = sanitizeString($_POST['end_datetime'] ?? '');
    
    // Validate inputs
    $errors = collectErrors([
        validateRequired($rentalId, 'Rental'),
        validateDateTime($endDateTime, 'End date/time')
    ]);
    
    // If no errors, complete rental
    if (empty($errors)) {
        // Get rental details before completing
        $rentalDetails = $rental->getRentalById($rentalId);
        
        if (!$rentalDetails) {
            $errors[] = "Rental not found";
        } else {
            // Verify user has permission (admin or owner)
            if (!Auth::isAdmin() && $rentalDetails['userId'] != Auth::getCurrentUserId()) {
                $errors[] = "You do not have permission to return this rental";
            } else {
                // Validate end time is after start time
                $startTime = new DateTime($rentalDetails['startDateTime']);
                $endTime = new DateTime($endDateTime);
                
                if ($endTime <= $startTime) {
                    $errors[] = "End date/time must be after start date/time";
                } else {
                    // Complete rental
                    $result = $rental->completeRental($rentalId, $endDateTime);
                    
                    if ($result === true) {
                        // Calculate total cost
                        $totalCost = $rental->calculateTotalCost(
                            $rentalDetails['startDateTime'],
                            $endDateTime,
                            $rentalDetails['costPerHourAtStart']
                        );
                        
                        $returnSuccess = true;
                        $returnInfo = [
                            'code' => $rentalDetails['motorbikeCode'],
                            'startDateTime' => $rentalDetails['startDateTime'],
                            'endDateTime' => $endDateTime,
                            'costPerHour' => $rentalDetails['costPerHourAtStart'],
                            'totalCost' => $totalCost
                        ];
                        
                        // Refresh active rentals
                        if (Auth::isAdmin()) {
                            $activeRentals = $rental->getAllActiveRentals();
                        } else {
                            $activeRentals = $rental->getActiveRentalsByUser(Auth::getCurrentUserId());
                        }
                    } else {
                        $errors[] = $result;
                    }
                }
            }
        }
    }
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2>Return Motorbike</h2>
    
    <?php if ($returnSuccess): ?>
        <!-- Return Success Notification -->
        <div class="notification-box">
            <h3>✓ Motorbike Returned Successfully!</h3>
            <p><strong>Motorbike Code:</strong> <?php echo htmlspecialchars($returnInfo['code']); ?></p>
            <p><strong>Start Date/Time:</strong> <?php echo htmlspecialchars($returnInfo['startDateTime']); ?></p>
            <p><strong>End Date/Time:</strong> <?php echo htmlspecialchars($returnInfo['endDateTime']); ?></p>
            <p><strong>Cost per Hour:</strong> $<?php echo number_format($returnInfo['costPerHour'], 2); ?></p>
            <p style="font-size: 1.3rem;"><strong>Total Cost to Pay:</strong> <span style="color: #2e7d32;">$<?php echo number_format($returnInfo['totalCost'], 2); ?></span></p>
        </div>
        <div class="mt-1">
            <a href="return.php" class="btn">Return Another Motorbike</a>
            <a href="rentals_history.php" class="btn btn-secondary">View Rental History</a>
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
        
        <?php if (empty($activeRentals)): ?>
            <div class="message info">
                <?php if (Auth::isAdmin()): ?>
                    No active rentals to return.
                <?php else: ?>
                    You don't have any active rentals to return.
                <?php endif; ?>
            </div>
            <div class="mt-1">
                <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
            </div>
        <?php else: ?>
            <form method="POST" action="return.php">
                <div class="form-group">
                    <label for="rental_id">Select Rental to Return *</label>
                    <select id="rental_id" name="rental_id" required>
                        <option value="">-- Select Rental --</option>
                        <?php foreach ($activeRentals as $r): ?>
                            <option value="<?php echo $r['rentalId']; ?>" <?php echo (isset($rentalId) && $rentalId == $r['rentalId']) ? 'selected' : ''; ?>>
                                <?php 
                                $displayText = "Rental #" . $r['rentalId'] . " - " . $r['motorbikeCode'] . " - " . $r['description'];
                                if (Auth::isAdmin()) {
                                    $displayText .= " (User: " . $r['name'] . " " . $r['surname'] . ")";
                                }
                                $displayText .= " - Started: " . $r['startDateTime'];
                                echo htmlspecialchars($displayText);
                                ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="end_datetime">End Date/Time *</label>
                    <input type="datetime-local" id="end_datetime" name="end_datetime" 
                           value="<?php echo isset($endDateTime) ? htmlspecialchars($endDateTime) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Return Motorbike</button>
                    <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
            
            <script>
                // Set default datetime to current time
                document.addEventListener('DOMContentLoaded', function() {
                    var datetimeInput = document.getElementById('end_datetime');
                    if (!datetimeInput.value) {
                        var now = new Date();
                        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
                        datetimeInput.value = now.toISOString().slice(0, 16);
                    }
                });
            </script>
            
            <?php if (!empty($activeRentals)): ?>
                <h3 class="mt-2">Active Rentals Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Rental ID</th>
                            <?php if (Auth::isAdmin()): ?>
                                <th>User</th>
                            <?php endif; ?>
                            <th>Motorbike</th>
                            <th>Location</th>
                            <th>Start Date/Time</th>
                            <th>Cost/Hour</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($activeRentals as $r): ?>
                            <tr>
                                <td><strong>#<?php echo $r['rentalId']; ?></strong></td>
                                <?php if (Auth::isAdmin()): ?>
                                    <td><?php echo htmlspecialchars($r['name'] . ' ' . $r['surname']); ?></td>
                                <?php endif; ?>
                                <td><?php echo htmlspecialchars($r['motorbikeCode']); ?></td>
                                <td><?php echo htmlspecialchars($r['rentingLocation']); ?></td>
                                <td><?php echo htmlspecialchars($r['startDateTime']); ?></td>
                                <td>$<?php echo number_format($r['costPerHourAtStart'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</main>

<?php include 'includes/footer.php'; ?>
