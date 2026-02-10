<?php
/**
 * Motorbikes List page
 * User: Shows available motorbikes only
 * Admin: Shows all/available/rented motorbikes based on filter
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';

// Require login
Auth::requireLogin();

$pageTitle = 'Motorbikes';
$motorbike = new Motorbike();

// Get filter and search parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$searchCode = isset($_GET['search_code']) ? trim($_GET['search_code']) : '';
$searchLocation = isset($_GET['search_location']) ? trim($_GET['search_location']) : '';
$searchDescription = isset($_GET['search_description']) ? trim($_GET['search_description']) : '';

// Check if search is active
$isSearching = !empty($searchCode) || !empty($searchLocation) || !empty($searchDescription);

// Get motorbikes based on search or filter
if ($isSearching) {
    // Perform search
    if (Auth::isAdmin()) {
        $motorbikes = $motorbike->searchMotorbikes($searchCode, $searchLocation, $searchDescription, false);
    } else {
        $motorbikes = $motorbike->searchMotorbikes($searchCode, $searchLocation, $searchDescription, true);
    }
    $listTitle = 'Search Results';
} else {
    // Get motorbikes based on user type and filter
    if (Auth::isAdmin()) {
        if ($filter === 'available') {
            $motorbikes = $motorbike->getAvailableMotorbikes();
            $listTitle = 'Available Motorbikes';
        } elseif ($filter === 'rented') {
            $motorbikes = $motorbike->getRentedMotorbikes();
            $listTitle = 'Currently Rented Motorbikes';
        } else {
            $motorbikes = $motorbike->getAllMotorbikes();
            $listTitle = 'All Motorbikes';
        }
    } else {
        // Regular users only see available motorbikes
        $motorbikes = $motorbike->getAvailableMotorbikes();
        $listTitle = 'Available Motorbikes';
    }
}

include 'includes/header.php';
include 'includes/nav.php';
?>

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
                <?php if (Auth::isAdmin()): ?>
                    <a href="motorbike_form.php" class="btn">Add New Motorbike</a>
                <?php endif; ?>
            </div>
        </form>
    </div>
    
    <?php if (Auth::isAdmin() && !$isSearching): ?>
        <!-- Filter options for admin -->
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
                    <?php if (Auth::isAdmin()): ?>
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
                        <?php if (Auth::isAdmin()): ?>
                            <td>
                                <a href="motorbike_form.php?code=<?php echo urlencode($bike['code']); ?>">Edit</a>
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

<?php include 'includes/footer.php'; ?>
