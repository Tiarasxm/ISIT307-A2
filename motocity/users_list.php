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

// Get filter
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

// Get users based on filter
if ($filter === 'renting') {
    $users = $user->getUsersCurrentlyRenting();
    $listTitle = 'Users Currently Renting Motorbikes';
} else {
    $users = $user->getAllUsers();
    $listTitle = 'All Users';
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2><?php echo $listTitle; ?></h2>
    
    <!-- Filter options -->
    <div class="mb-2">
        <a href="users_list.php?filter=all" class="btn <?php echo $filter === 'all' ? '' : 'btn-secondary'; ?> btn-small">All Users</a>
        <a href="users_list.php?filter=renting" class="btn <?php echo $filter === 'renting' ? '' : 'btn-secondary'; ?> btn-small">Currently Renting</a>
    </div>
    
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
        <a href="user_search.php" class="btn">Search Users</a>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
