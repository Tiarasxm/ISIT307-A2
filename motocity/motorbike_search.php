<?php
/**
 * Motorbike Search page
 * Search by code, location, or description (partial match, combinable)
 * User: Searches available motorbikes only
 * Admin: Searches all motorbikes
 */
require_once 'includes/config.php';
require_once 'includes/validation.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';

// Require login
Auth::requireLogin();

$pageTitle = 'Search Motorbike';
$motorbike = new Motorbike();
$searchResults = null;
$searched = false;

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $code = sanitizeString($_GET['code'] ?? '');
    $location = sanitizeString($_GET['location'] ?? '');
    $description = sanitizeString($_GET['description'] ?? '');
    
    $searched = true;
    
    // Search based on user type
    if (Auth::isAdmin()) {
        $searchResults = $motorbike->searchMotorbikes($code, $location, $description);
    } else {
        $searchResults = $motorbike->searchAvailableMotorbikes($code, $location, $description);
    }
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2>Search Motorbike</h2>
    
    <div class="search-form">
        <form method="GET" action="motorbike_search.php">
            <p><strong>Search by one or more fields (partial match supported):</strong></p>
            
            <div class="form-group">
                <label for="code">Code</label>
                <input type="text" id="code" name="code" value="<?php echo isset($_GET['code']) ? htmlspecialchars($_GET['code']) : ''; ?>" placeholder="e.g., MB">
            </div>
            
            <div class="form-group">
                <label for="location">Renting Location</label>
                <input type="text" id="location" name="location" value="<?php echo isset($_GET['location']) ? htmlspecialchars($_GET['location']) : ''; ?>" placeholder="e.g., Downtown">
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" id="description" name="description" value="<?php echo isset($_GET['description']) ? htmlspecialchars($_GET['description']) : ''; ?>" placeholder="e.g., Honda">
            </div>
            
            <div class="form-group">
                <button type="submit" name="search" class="btn">Search</button>
                <a href="motorbike_search.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
    
    <?php if ($searched): ?>
        <h3>Search Results</h3>
        
        <?php if (empty($searchResults)): ?>
            <div class="message info">
                No motorbikes found matching your search criteria.
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
                    <?php foreach ($searchResults as $bike): ?>
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
            
            <p class="mt-1"><strong>Total:</strong> <?php echo count($searchResults); ?> motorbike(s) found</p>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="mt-2">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
