<?php
// Add realistic sample active rentals
require_once 'classes/Database.php';

$conn = Database::getInstance()->getConnection();

// First, clear existing active rentals to avoid conflicts
$conn->query("DELETE FROM rentals WHERE status = 'ACTIVE'");

// Add sample active rentals (started at different times today, all at least 1 hour ago)
$now = new DateTime();

// Rental 1: Started 2 hours ago (User Wei Ming - MB001)
$start1 = clone $now;
$start1->modify('-2 hours');

// Rental 2: Started 3.5 hours ago (User Mei Ling - MB002)
$start2 = clone $now;
$start2->modify('-3 hours -30 minutes');

// Rental 3: Started 1.5 hours ago (User Raj - MB005)
$start3 = clone $now;
$start3->modify('-1 hours -30 minutes');

// Rental 4: Started 5 hours ago (User Sarah - MB006)
$start4 = clone $now;
$start4->modify('-5 hours');

$sql = "INSERT INTO rentals (userId, motorbikeCode, startDateTime, costPerHourAtStart, status) VALUES
    (2, 'MB001', ?, 15.00, 'ACTIVE'),
    (3, 'MB002', ?, 18.00, 'ACTIVE'),
    (4, 'MB005', ?, 8.00, 'ACTIVE'),
    (5, 'MB006', ?, 10.00, 'ACTIVE')";

$stmt = $conn->prepare($sql);
$time1 = $start1->format('Y-m-d H:i:s');
$time2 = $start2->format('Y-m-d H:i:s');
$time3 = $start3->format('Y-m-d H:i:s');
$time4 = $start4->format('Y-m-d H:i:s');

$stmt->bind_param("ssss", $time1, $time2, $time3, $time4);

if ($stmt->execute()) {
    echo "<h3>✓ Sample active rentals added successfully!</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background-color: #2C3E50; color: white;'><th>User</th><th>Motorbike</th><th>Started</th><th>Duration</th><th>Cost/Hour</th></tr>";
    echo "<tr><td>Wei Ming Lim</td><td>MB001</td><td>2 hours ago</td><td>~2 hours</td><td>$15.00</td></tr>";
    echo "<tr><td>Mei Ling Tan</td><td>MB002</td><td>3.5 hours ago</td><td>~3.5 hours</td><td>$18.00</td></tr>";
    echo "<tr><td>Raj Kumar</td><td>MB005</td><td>1.5 hours ago</td><td>~1.5 hours</td><td>$8.00</td></tr>";
    echo "<tr><td>Sarah Chen</td><td>MB006</td><td>5 hours ago</td><td>~5 hours</td><td>$10.00</td></tr>";
    echo "</table>";
    echo "<p><strong>Expected costs when returned now:</strong></p>";
    echo "<ul>";
    echo "<li>MB001: ~$30.00 (2 hours × $15)</li>";
    echo "<li>MB002: ~$63.00 (3.5 hours × $18)</li>";
    echo "<li>MB005: ~$12.00 (1.5 hours × $8)</li>";
    echo "<li>MB006: ~$50.00 (5 hours × $10)</li>";
    echo "</ul>";
    echo "<br><a href='return.php' style='padding: 10px 20px; background-color: #FF9A6C; color: white; text-decoration: none; border-radius: 4px;'>Go to Return Page</a> ";
    echo "<a href='dashboard.php' style='padding: 10px 20px; background-color: #2C3E50; color: white; text-decoration: none; border-radius: 4px;'>Dashboard</a>";
} else {
    echo "Error: " . $stmt->error;
}

$conn->close();
?>
