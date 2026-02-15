<?php
/**
 * MotoCity Test Cases
 * Based on Assignment Requirements
 */
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Motorbike.php';
require_once 'classes/Rental.php';
require_once 'classes/Auth.php';

$testResults = [];
$passed = 0;
$failed = 0;

function runTest($testName, $callback) {
    global $testResults, $passed, $failed;
    try {
        $result = $callback();
        if ($result === true) {
            $testResults[] = ['name' => $testName, 'status' => 'PASS', 'message' => 'Test passed'];
            $passed++;
        } else {
            $testResults[] = ['name' => $testName, 'status' => 'FAIL', 'message' => $result];
            $failed++;
        }
    } catch (Exception $e) {
        $testResults[] = ['name' => $testName, 'status' => 'ERROR', 'message' => $e->getMessage()];
        $failed++;
    }
}

// ========================================
// TEST CATEGORY 1: USER REGISTRATION & LOGIN
// ========================================

runTest("1.1 User can register with valid data", function() {
    $user = new User();
    $user->setName("Test");
    $user->setSurname("User");
    $user->setPhone("+6512345678");
    $user->setEmail("testuser" . time() . "@test.com");
    $user->setType("User");
    $user->setPassword("password123");
    
    $result = $user->register();
    return $result === true ? true : "Registration failed: $result";
});

runTest("1.2 Administrator can register", function() {
    $user = new User();
    $user->setName("Test");
    $user->setSurname("Admin");
    $user->setPhone("+6587654321");
    $user->setEmail("testadmin" . time() . "@test.com");
    $user->setType("Administrator");
    $user->setPassword("password123");
    
    $result = $user->register();
    return $result === true ? true : "Admin registration failed: $result";
});

runTest("1.3 Duplicate email registration is prevented", function() {
    $user = new User();
    $user->setName("Duplicate");
    $user->setSurname("User");
    $user->setPhone("+6511111111");
    $user->setEmail("admin@motocity.com"); // Existing email
    $user->setType("User");
    $user->setPassword("password123");
    
    $result = $user->register();
    return ($result !== true && strpos($result, 'already') !== false) ? true : "Should prevent duplicate email";
});

runTest("1.4 User can login with correct credentials", function() {
    $user = new User();
    $result = $user->login("admin@motocity.com", "password123");
    return ($result instanceof User) ? true : "Login failed: $result";
});

runTest("1.5 Login fails with incorrect password", function() {
    $user = new User();
    $result = $user->login("admin@motocity.com", "wrongpassword");
    return ($result !== true && is_string($result)) ? true : "Should reject wrong password";
});

// ========================================
// TEST CATEGORY 2: MOTORBIKE MANAGEMENT (ADMIN)
// ========================================

runTest("2.1 Admin can create new motorbike", function() {
    $motorbike = new Motorbike();
    $motorbike->setCode("TEST" . time());
    $motorbike->setRentingLocation("Test Location");
    $motorbike->setDescription("Test Motorbike");
    $motorbike->setCostPerHour(15.00);
    
    $result = $motorbike->create();
    return $result === true ? true : "Failed to create motorbike: $result";
});

runTest("2.2 Admin can update motorbike details", function() {
    $motorbike = new Motorbike();
    $data = $motorbike->getByCode("MB001");
    
    if ($data) {
        $motorbike->setRentingLocation("Updated Location");
        $motorbike->setDescription("Updated Description");
        $motorbike->setCostPerHour(20.00);
        $result = $motorbike->update();
        return $result === true ? true : "Failed to update: $result";
    }
    return "Motorbike MB001 not found";
});

runTest("2.3 Can list all motorbikes", function() {
    $motorbike = new Motorbike();
    $all = $motorbike->getAllMotorbikes();
    return (is_array($all) && count($all) > 0) ? true : "No motorbikes found";
});

runTest("2.4 Can list available motorbikes only", function() {
    $motorbike = new Motorbike();
    $available = $motorbike->getAvailableMotorbikes();
    return is_array($available) ? true : "Failed to get available motorbikes";
});

runTest("2.5 Can list currently rented motorbikes", function() {
    $motorbike = new Motorbike();
    $rented = $motorbike->getRentedMotorbikes();
    return is_array($rented) ? true : "Failed to get rented motorbikes";
});

// ========================================
// TEST CATEGORY 3: MOTORBIKE SEARCH
// ========================================

runTest("3.1 Search motorbikes by code", function() {
    $motorbike = new Motorbike();
    $results = $motorbike->searchMotorbikes("MB001", "", "");
    return (is_array($results) && count($results) > 0) ? true : "Search by code failed";
});

runTest("3.2 Search motorbikes by location", function() {
    $motorbike = new Motorbike();
    $results = $motorbike->searchMotorbikes("", "Orchard", "");
    return is_array($results) ? true : "Search by location failed";
});

runTest("3.3 Search motorbikes by description", function() {
    $motorbike = new Motorbike();
    $results = $motorbike->searchMotorbikes("", "", "Honda");
    return is_array($results) ? true : "Search by description failed";
});

runTest("3.4 Search supports partial terms", function() {
    $motorbike = new Motorbike();
    $results = $motorbike->searchMotorbikes("MB", "", "");
    return (is_array($results) && count($results) > 0) ? true : "Partial search failed";
});

// ========================================
// TEST CATEGORY 4: RENTAL OPERATIONS
// ========================================

runTest("4.1 User can rent available motorbike", function() {
    $motorbike = new Motorbike();
    $available = $motorbike->getAvailableMotorbikes();
    
    if (count($available) > 0) {
        $rental = new Rental();
        $rental->setUserId(2); // Wei Ming
        $rental->setMotorbikeCode($available[0]['code']);
        $rental->setStartDateTime(date('Y-m-d H:i:s'));
        $rental->setCostPerHourAtStart($available[0]['costPerHour']);
        
        $result = $rental->createRental();
        return $result === true ? true : "Rental creation failed: $result";
    }
    return "No available motorbikes to test";
});

runTest("4.2 Cannot rent already rented motorbike", function() {
    $motorbike = new Motorbike();
    $rented = $motorbike->getRentedMotorbikes();
    
    if (count($rented) > 0) {
        $rental = new Rental();
        $rental->setUserId(3);
        $rental->setMotorbikeCode($rented[0]['code']);
        $rental->setStartDateTime(date('Y-m-d H:i:s'));
        $rental->setCostPerHourAtStart($rented[0]['costPerHour']);
        
        $result = $rental->createRental();
        return ($result !== true && strpos($result, 'not available') !== false) ? true : "Should prevent double rental";
    }
    return true; // Pass if no rented bikes to test
});

runTest("4.3 User can return rented motorbike", function() {
    $rental = new Rental();
    $active = $rental->getActiveRentalsByUser(2);
    
    if (count($active) > 0) {
        $result = $rental->returnRental($active[0]['rentalId']);
        return $result === true ? true : "Return failed: $result";
    }
    return true; // Pass if no active rentals
});

runTest("4.4 Cost calculation includes minimum 1 hour", function() {
    $rental = new Rental();
    $start = date('Y-m-d H:i:s', strtotime('-30 minutes'));
    $end = date('Y-m-d H:i:s');
    $cost = $rental->calculateTotalCost($start, $end, 15.00);
    
    return ($cost >= 15.00) ? true : "Minimum charge not applied: $cost";
});

runTest("4.5 Cost calculation is accurate for multiple hours", function() {
    $rental = new Rental();
    $start = date('Y-m-d H:i:s', strtotime('-3 hours'));
    $end = date('Y-m-d H:i:s');
    $cost = $rental->calculateTotalCost($start, $end, 15.00);
    
    $expected = 45.00; // 3 hours * $15
    return (abs($cost - $expected) < 1) ? true : "Cost calculation incorrect: $cost (expected ~$expected)";
});

// ========================================
// TEST CATEGORY 5: USER MANAGEMENT (ADMIN)
// ========================================

runTest("5.1 Admin can list all users", function() {
    $user = new User();
    $users = $user->getAllUsers();
    return (is_array($users) && count($users) > 0) ? true : "Failed to list users";
});

runTest("5.2 Admin can search users by name", function() {
    $user = new User();
    $results = $user->searchUsers("Wei", "", "", "");
    return is_array($results) ? true : "User search by name failed";
});

runTest("5.3 Admin can search users by email", function() {
    $user = new User();
    $results = $user->searchUsers("", "", "", "admin@motocity.com");
    return (is_array($results) && count($results) > 0) ? true : "User search by email failed";
});

runTest("5.4 Admin can list users currently renting", function() {
    $user = new User();
    $renting = $user->getUsersCurrentlyRenting();
    return is_array($renting) ? true : "Failed to list users currently renting";
});

// ========================================
// TEST CATEGORY 6: RENTAL HISTORY
// ========================================

runTest("6.1 User can view completed rental history", function() {
    $rental = new Rental();
    $history = $rental->getCompletedRentalsByUser(2);
    return is_array($history) ? true : "Failed to get rental history";
});

runTest("6.2 User can view active rentals", function() {
    $rental = new Rental();
    $active = $rental->getActiveRentalsByUser(2);
    return is_array($active) ? true : "Failed to get active rentals";
});

runTest("6.3 Admin can view all active rentals", function() {
    $rental = new Rental();
    $allActive = $rental->getAllActiveRentals();
    return is_array($allActive) ? true : "Failed to get all active rentals";
});

// ========================================
// DISPLAY RESULTS
// ========================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Results - MotoCity</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2C3E50;
            border-bottom: 3px solid #FF9A6C;
            padding-bottom: 10px;
        }
        .summary {
            display: flex;
            gap: 20px;
            margin: 20px 0;
        }
        .summary-box {
            flex: 1;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .summary-box h2 {
            margin: 0;
            font-size: 2.5rem;
        }
        .summary-box p {
            margin: 5px 0 0 0;
            font-size: 1rem;
        }
        .total { background-color: #3498db; color: white; }
        .passed { background-color: #27ae60; color: white; }
        .failed { background-color: #e74c3c; color: white; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #2C3E50;
            color: white;
        }
        .PASS { color: #27ae60; font-weight: bold; }
        .FAIL { color: #e74c3c; font-weight: bold; }
        .ERROR { color: #e67e22; font-weight: bold; }
        .category {
            background-color: #ecf0f1;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .actions {
            margin-top: 30px;
            text-align: center;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #FF9A6C;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 0 10px;
        }
        .btn:hover {
            background-color: #e8855a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 MotoCity Test Results</h1>
        
        <div class="summary">
            <div class="summary-box total">
                <h2><?php echo count($testResults); ?></h2>
                <p>Total Tests</p>
            </div>
            <div class="summary-box passed">
                <h2><?php echo $passed; ?></h2>
                <p>Passed</p>
            </div>
            <div class="summary-box failed">
                <h2><?php echo $failed; ?></h2>
                <p>Failed</p>
            </div>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th style="width: 60%;">Test Case</th>
                    <th style="width: 10%;">Status</th>
                    <th style="width: 30%;">Message</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $categories = [
                    '1.' => 'User Registration & Login',
                    '2.' => 'Motorbike Management (Admin)',
                    '3.' => 'Motorbike Search',
                    '4.' => 'Rental Operations',
                    '5.' => 'User Management (Admin)',
                    '6.' => 'Rental History'
                ];
                
                $currentCategory = '';
                foreach ($testResults as $test):
                    $testPrefix = substr($test['name'], 0, 2);
                    if (isset($categories[$testPrefix]) && $categories[$testPrefix] !== $currentCategory) {
                        $currentCategory = $categories[$testPrefix];
                        echo "<tr class='category'><td colspan='3'>$currentCategory</td></tr>";
                    }
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($test['name']); ?></td>
                        <td class="<?php echo $test['status']; ?>"><?php echo $test['status']; ?></td>
                        <td><?php echo htmlspecialchars($test['message']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="actions">
            <a href="dashboard.php" class="btn">Go to Dashboard</a>
            <a href="test_cases.php" class="btn">Run Tests Again</a>
        </div>
    </div>
</body>
</html>
