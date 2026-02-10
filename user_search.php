<?php
/**
 * User Search page (Admin only)
 * Search by name, surname, phone, or email (partial match, combinable)
 */
require_once 'includes/config.php';
require_once 'includes/validation.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

// Require admin access
Auth::requireAdmin();

$pageTitle = 'Search User';
$user = new User();
$searchResults = null;
$searched = false;

// Handle search
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $name = sanitizeString($_GET['name'] ?? '');
    $surname = sanitizeString($_GET['surname'] ?? '');
    $phone = sanitizeString($_GET['phone'] ?? '');
    $email = sanitizeString($_GET['email'] ?? '');
    
    $searched = true;
    $searchResults = $user->searchUsers($name, $surname, $phone, $email);
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2>Search User</h2>
    
    <div class="search-form">
        <form method="GET" action="user_search.php">
            <p><strong>Search by one or more fields (partial match supported):</strong></p>
            
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" id="name" name="name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>" placeholder="e.g., John">
            </div>
            
            <div class="form-group">
                <label for="surname">Surname</label>
                <input type="text" id="surname" name="surname" value="<?php echo isset($_GET['surname']) ? htmlspecialchars($_GET['surname']) : ''; ?>" placeholder="e.g., Smith">
            </div>
            
            <div class="form-group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?>" placeholder="e.g., 555">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" id="email" name="email" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>" placeholder="e.g., @example.com">
            </div>
            
            <div class="form-group">
                <button type="submit" name="search" class="btn">Search</button>
                <a href="user_search.php" class="btn btn-secondary">Clear</a>
            </div>
        </form>
    </div>
    
    <?php if ($searched): ?>
        <h3>Search Results</h3>
        
        <?php if (empty($searchResults)): ?>
            <div class="message info">
                No users found matching your search criteria.
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
                    <?php foreach ($searchResults as $u): ?>
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
            
            <p class="mt-1"><strong>Total:</strong> <?php echo count($searchResults); ?> user(s) found</p>
        <?php endif; ?>
    <?php endif; ?>
    
    <div class="mt-2">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        <a href="users_list.php" class="btn">View All Users</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
