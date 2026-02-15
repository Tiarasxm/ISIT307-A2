<?php
session_start();

// Require login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'db_connect.php';

$user_type = $_SESSION['user_type'];
$is_admin = ($user_type === 'Administrator');

// Get filter and search parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search_code = isset($_GET['search_code']) ? mysqli_real_escape_string($conn, trim($_GET['search_code'])) : '';
$search_location = isset($_GET['search_location']) ? mysqli_real_escape_string($conn, trim($_GET['search_location'])) : '';
$search_description = isset($_GET['search_description']) ? mysqli_real_escape_string($conn, trim($_GET['search_description'])) : '';

$is_searching = !empty($search_code) || !empty($search_location) || !empty($search_description);

// Build SQL query
if ($is_searching) {
    $sql = "SELECT * FROM motorbikes WHERE 1=1";
    if (!empty($search_code)) {
        $sql .= " AND code LIKE '%$search_code%'";
    }
    if (!empty($search_location)) {
        $sql .= " AND rentingLocation LIKE '%$search_location%'";
    }
    if (!empty($search_description)) {
        $sql .= " AND description LIKE '%$search_description%'";
    }
    
    // If not admin, only show available
    if (!$is_admin) {
        $sql .= " AND code NOT IN (SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE')";
    }
    $list_title = "Search Results";
} else {
    // Get motorbikes based on filter
    if ($is_admin) {
        if ($filter === 'available') {
            $sql = "SELECT * FROM motorbikes WHERE code NOT IN (SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE')";
            $list_title = "Available Motorbikes";
        } elseif ($filter === 'rented') {
            $sql = "SELECT DISTINCT m.* FROM motorbikes m INNER JOIN rentals r ON m.code = r.motorbikeCode WHERE r.status = 'ACTIVE'";
            $list_title = "Currently Rented Motorbikes";
        } else {
            $sql = "SELECT * FROM motorbikes";
            $list_title = "All Motorbikes";
        }
    } else {
        $sql = "SELECT * FROM motorbikes WHERE code NOT IN (SELECT motorbikeCode FROM rentals WHERE status = 'ACTIVE')";
        $list_title = "Available Motorbikes";
    }
}

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorbikes - MotoCity</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            max-width: 1000px;
            padding: 2.5rem 2rem;
            margin-top: 4rem;
        }
        .card {
            padding: 1.5rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>MotoCity</h1>
        </div>
    </header>
    
    <nav>
        <div class="container">
            <ul>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="motorbikes_list.php" class="active">Motorbikes</a></li>
                <li><a href="logout.php">Logout (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
            </ul>
        </div>
    </nav>

    <main class="container">
        <h2><?php echo $list_title; ?></h2>
        
        <!-- Search Form -->
        <div class="card">
            <h3>Search Motorbikes</h3>
            <form method="GET" action="motorbikes_list.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="search_code">Code</label>
                        <input type="text" id="search_code" name="search_code" value="<?php echo htmlspecialchars($search_code); ?>" placeholder="e.g., MB001">
                    </div>
                    
                    <div class="form-group">
                        <label for="search_location">Location</label>
                        <input type="text" id="search_location" name="search_location" value="<?php echo htmlspecialchars($search_location); ?>" placeholder="e.g., Orchard">
                    </div>
                    
                    <div class="form-group">
                        <label for="search_description">Description</label>
                        <input type="text" id="search_description" name="search_description" value="<?php echo htmlspecialchars($search_description); ?>" placeholder="e.g., Honda">
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Search</button>
                    <a href="motorbikes_list.php" class="btn btn-secondary">Clear Search</a>
                </div>
            </form>
        </div>
        
        <?php if ($is_admin && !$is_searching): ?>
            <!-- Filter options for admin -->
            <div class="mb-2">
                <a href="motorbikes_list.php?filter=all" class="btn <?php echo $filter === 'all' ? '' : 'btn-secondary'; ?> btn-small">All Motorbikes</a>
                <a href="motorbikes_list.php?filter=available" class="btn <?php echo $filter === 'available' ? '' : 'btn-secondary'; ?> btn-small">Available</a>
                <a href="motorbikes_list.php?filter=rented" class="btn <?php echo $filter === 'rented' ? '' : 'btn-secondary'; ?> btn-small">Currently Rented</a>
            </div>
        <?php endif; ?>
        
        <?php if ($result->num_rows == 0): ?>
            <div class="message info">
                No motorbikes found.
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Location</th>
                        <th>Description</th>
                        <th>Cost per Hour</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($bike = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($bike['code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($bike['rentingLocation']); ?></td>
                            <td><?php echo htmlspecialchars($bike['description']); ?></td>
                            <td>$<?php echo number_format($bike['costPerHour'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <p class="mt-1"><strong>Total:</strong> <?php echo $result->num_rows; ?> motorbike(s)</p>
        <?php endif; ?>
        
        <div class="mt-2">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </main>
</body>
</html>
<?php $conn->close(); ?>
