<?php
/**
 * Users List page (Admin only)
 * Shows all users or users currently renting motorbikes
 */
require_once 'includes/config.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

// Require admin access
Auth::requireAdmin();

$pageTitle = 'Users';
$user = new User();

// Get filter and search parameters
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$searchName = isset($_GET['search_name']) ? trim($_GET['search_name']) : '';
$searchSurname = isset($_GET['search_surname']) ? trim($_GET['search_surname']) : '';
$searchPhone = isset($_GET['search_phone']) ? trim($_GET['search_phone']) : '';
$searchEmail = isset($_GET['search_email']) ? trim($_GET['search_email']) : '';

// Check if search is active
$isSearching = !empty($searchName) || !empty($searchSurname) || !empty($searchPhone) || !empty($searchEmail);

// Get users based on search or filter
if ($isSearching) {
    // Perform search
    $users = $user->searchUsers($searchName, $searchSurname, $searchPhone, $searchEmail);
    $listTitle = 'Search Results';
} else {
    // Get users based on filter
    if ($filter === 'renting') {
        $users = $user->getUsersCurrentlyRenting();
        $listTitle = 'Users Currently Renting Motorbikes';
    } else {
        $users = $user->getAllUsers();
        $listTitle = 'All Users';
    }
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2><?php echo $listTitle; ?></h2>
    
    <!-- Search Form -->
    <div class="card">
        <h3>Search Users</h3>
        <form method="GET" action="users_list.php">
            <div class="form-row">
                <div class="form-group">
                    <label for="search_name">Name</label>
                    <input type="text" id="search_name" name="search_name" value="<?php echo htmlspecialchars($searchName); ?>" placeholder="e.g., Wei Ming">
                </div>
                
                <div class="form-group">
                    <label for="search_surname">Surname</label>
                    <input type="text" id="search_surname" name="search_surname" value="<?php echo htmlspecialchars($searchSurname); ?>" placeholder="e.g., Lim">
                </div>
                
                <div class="form-group">
                    <label for="search_phone">Phone</label>
                    <input type="text" id="search_phone" name="search_phone" value="<?php echo htmlspecialchars($searchPhone); ?>" placeholder="e.g., 91234568 or +6591234568">
                </div>
                
                <div class="form-group">
                    <label for="search_email">Email</label>
                    <input type="text" id="search_email" name="search_email" value="<?php echo htmlspecialchars($searchEmail); ?>" placeholder="e.g., example@email.com">
                </div>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Search</button>
                <a href="users_list.php" class="btn btn-secondary">Clear Search</a>
            </div>
        </form>
    </div>
    
    <?php if (!$isSearching): ?>
        <!-- Filter options -->
        <div class="mb-2">
            <a href="users_list.php?filter=all" class="btn <?php echo $filter === 'all' ? '' : 'btn-secondary'; ?> btn-small">All Users</a>
            <a href="users_list.php?filter=renting" class="btn <?php echo $filter === 'renting' ? '' : 'btn-secondary'; ?> btn-small">Currently Renting</a>
        </div>
    <?php endif; ?>
    
    <?php if (empty($users)): ?>
        <div class="message info">
            No users found.
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Surname</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><?php echo htmlspecialchars($u['name']); ?></td>
                        <td><?php echo htmlspecialchars($u['surname']); ?></td>
                        <td><?php echo htmlspecialchars($u['phone']); ?></td>
                        <td><?php echo htmlspecialchars($u['email']); ?></td>
                        <td>
                            <?php if ($u['type'] === 'Administrator'): ?>
                                <span class="badge badge-danger"><?php echo htmlspecialchars($u['type']); ?></span>
                            <?php else: ?>
                                <span class="badge badge-info"><?php echo htmlspecialchars($u['type']); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <p class="mt-1"><strong>Total:</strong> <?php echo count($users); ?> user(s)</p>
    <?php endif; ?>
    
    <div class="mt-2">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
