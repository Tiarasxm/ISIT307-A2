<?php
// Clean up unrealistic rental data
require_once 'classes/Database.php';
require_once 'classes/Rental.php';

$conn = Database::getInstance()->getConnection();
$rental = new Rental();

// Get all completed rentals
$result = $conn->query("SELECT * FROM rentals WHERE status = 'COMPLETED'");
$rentals = $result->fetch_all(MYSQLI_ASSOC);

$deleted = 0;

foreach ($rentals as $r) {
    $cost = $rental->calculateTotalCost($r['startDateTime'], $r['endDateTime'], $r['costPerHourAtStart']);
    
    // Delete if cost is over $1000 (unrealistic)
    if ($cost > 1000) {
        $stmt = $conn->prepare("DELETE FROM rentals WHERE rentalId = ?");
        $stmt->bind_param("i", $r['rentalId']);
        $stmt->execute();
        $deleted++;
        echo "✓ Deleted rental ID {$r['rentalId']} - Cost was $" . number_format($cost, 2) . "<br>";
    }
}

echo "<br><strong>Total deleted: $deleted rental(s)</strong><br><br>";
echo "<a href='dashboard.php'>Go to Dashboard</a>";

$conn->close();
?>
