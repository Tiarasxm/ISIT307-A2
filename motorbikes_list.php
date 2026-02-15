<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';

Auth::requireLogin();

$isAdmin = Auth::isAdmin();
$filter = $_GET['filter'] ?? 'all';
$searchCode = trim($_GET['search_code'] ?? '');
$searchLocation = trim($_GET['search_location'] ?? '');
$searchDescription = trim($_GET['search_description'] ?? '');

$isSearching = !empty($searchCode) || !empty($searchLocation) || !empty($searchDescription);

$motorbike = new Motorbike();

if ($isSearching) {
    $motorbikes = $motorbike->searchMotorbikes($searchCode, $searchLocation, $searchDescription);
    $listTitle = "Search Results";
} else {
    if ($isAdmin) {
        if ($filter === 'available') {
            $motorbikes = $motorbike->getAvailableMotorbikes();
            $listTitle = "Available Motorbikes";
        } elseif ($filter === 'rented') {
            $motorbikes = $motorbike->getRentedMotorbikes();
            $listTitle = "Currently Rented Motorbikes";
        } else {
            $motorbikes = $motorbike->getAllMotorbikes();
            $listTitle = "All Motorbikes";
        }
    } else {
        $motorbikes = $motorbike->getAvailableMotorbikes();
        $listTitle = "Available Motorbikes";
    }
}
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
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/nav.php'; ?>

    <main class="container">
        <h2><?php echo $listTitle; ?></h2>
        
        <!-- Search Form -->
        <div class="card">
            <h3>Search Motorbikes</h3>
            <form method="GET" action="motorbikes_list.php">
                <div class="form-row">
                    <div class="form-group">
                        <label for="search_code">Code</label>
                        <input type="text" id="search_code" name="search_code" value="<?php echo htmlspecialchars($searchCode); ?>" placeholder="e.g., MB001">
                    </div>
                    
                    <div class="form-group">
                        <label for="search_location">Location</label>
                        <input type="text" id="search_location" name="search_location" value="<?php echo htmlspecialchars($searchLocation); ?>" placeholder="e.g., Orchard">
                    </div>
                    
                    <div class="form-group">
                        <label for="search_description">Description</label>
                        <input type="text" id="search_description" name="search_description" value="<?php echo htmlspecialchars($searchDescription); ?>" placeholder="e.g., Honda">
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn">Search</button>
                    <a href="motorbikes_list.php" class="btn btn-secondary">Clear Search</a>
                    <?php if ($isAdmin): ?>
                        <a href="motorbike_form.php" class="btn">Add New Motorbike</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <?php if ($isAdmin && !$isSearching): ?>
            <div class="mb-2">
                <a href="motorbikes_list.php?filter=all" class="btn <?php echo $filter === 'all' ? '' : 'btn-secondary'; ?> btn-small">All Motorbikes</a>
                <a href="motorbikes_list.php?filter=available" class="btn <?php echo $filter === 'available' ? '' : 'btn-secondary'; ?> btn-small">Available</a>
                <a href="motorbikes_list.php?filter=rented" class="btn <?php echo $filter === 'rented' ? '' : 'btn-secondary'; ?> btn-small">Currently Rented</a>
            </div>
        <?php endif; ?>
        
        <?php if (empty($motorbikes)): ?>
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
                        <?php if ($isAdmin): ?>
                            <th>Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($motorbikes as $bike): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($bike['code']); ?></strong></td>
                            <td><?php echo htmlspecialchars($bike['rentingLocation']); ?></td>
                            <td><?php echo htmlspecialchars($bike['description']); ?></td>
                            <td>$<?php echo number_format($bike['costPerHour'], 2); ?></td>
                            <?php if ($isAdmin): ?>
                                <td>
                                    <a href="motorbike_form.php?code=<?php echo urlencode($bike['code']); ?>" class="btn btn-small">Edit</a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <p class="mt-1"><strong>Total:</strong> <?php echo count($motorbikes); ?> motorbike(s)</p>
        <?php endif; ?>
        
        <div class="mt-2">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </main>
</body>
</html>
