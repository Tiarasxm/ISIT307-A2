<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

Auth::requireAdmin();

$filter = $_GET['filter'] ?? 'all';
$searchName = trim($_GET['search_name'] ?? '');
$searchSurname = trim($_GET['search_surname'] ?? '');
$searchPhone = trim($_GET['search_phone'] ?? '');
$searchEmail = trim($_GET['search_email'] ?? '');

$isSearching = !empty($searchName) || !empty($searchSurname) || !empty($searchPhone) || !empty($searchEmail);

$user = new User();

if ($isSearching) {
    $users = $user->searchUsers($searchName, $searchSurname, $searchPhone, $searchEmail);
    $listTitle = "Search Results";
} elseif ($filter === 'renting') {
    $users = $user->getUsersCurrentlyRenting();
    $listTitle = "Users Currently Renting";
} else {
    $users = $user->getAllUsers();
    $listTitle = "All Users";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users - MotoCity</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            max-width: 1000px;
            padding: 2.5rem 2rem;
            margin-top: 4rem;
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
                        <input type="text" id="search_phone" name="search_phone" value="<?php echo htmlspecialchars($searchPhone); ?>" placeholder="e.g., 91234568">
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
                            <td><?php echo htmlspecialchars($u['name']); ?></td>
                            <td><?php echo htmlspecialchars($u['surname']); ?></td>
                            <td><?php echo htmlspecialchars($u['phone']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td><span class="badge badge-info"><?php echo htmlspecialchars($u['type']); ?></span></td>
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
</body>
</html>
