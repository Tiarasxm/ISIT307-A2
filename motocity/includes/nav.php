<?php
// Get current page name
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<nav>
    <div class="container">
        <ul>
            <?php if (Auth::isLoggedIn()): ?>
                <!-- Common links for all logged-in users -->
                <li><a href="dashboard.php" <?php echo $currentPage == 'dashboard.php' ? 'class="active"' : ''; ?>>Dashboard</a></li>
                
                <?php if (Auth::isAdmin()): ?>
                    <!-- Administrator menu -->
                    <li><a href="motorbikes_list.php" <?php echo $currentPage == 'motorbikes_list.php' ? 'class="active"' : ''; ?>>Motorbikes</a></li>
                    <li><a href="motorbike_form.php" <?php echo $currentPage == 'motorbike_form.php' ? 'class="active"' : ''; ?>>Add Motorbike</a></li>
                    <li><a href="motorbike_search.php" <?php echo $currentPage == 'motorbike_search.php' ? 'class="active"' : ''; ?>>Search Motorbike</a></li>
                    <li><a href="rent.php" <?php echo $currentPage == 'rent.php' ? 'class="active"' : ''; ?>>Rent Motorbike</a></li>
                    <li><a href="return.php" <?php echo $currentPage == 'return.php' ? 'class="active"' : ''; ?>>Return Motorbike</a></li>
                    <li><a href="rentals_current.php" <?php echo $currentPage == 'rentals_current.php' ? 'class="active"' : ''; ?>>Active Rentals</a></li>
                    <li><a href="users_list.php" <?php echo $currentPage == 'users_list.php' ? 'class="active"' : ''; ?>>Users</a></li>
                    <li><a href="user_search.php" <?php echo $currentPage == 'user_search.php' ? 'class="active"' : ''; ?>>Search User</a></li>
                <?php else: ?>
                    <!-- User menu -->
                    <li><a href="motorbikes_list.php" <?php echo $currentPage == 'motorbikes_list.php' ? 'class="active"' : ''; ?>>Available Motorbikes</a></li>
                    <li><a href="motorbike_search.php" <?php echo $currentPage == 'motorbike_search.php' ? 'class="active"' : ''; ?>>Search Motorbike</a></li>
                    <li><a href="rent.php" <?php echo $currentPage == 'rent.php' ? 'class="active"' : ''; ?>>Rent Motorbike</a></li>
                    <li><a href="return.php" <?php echo $currentPage == 'return.php' ? 'class="active"' : ''; ?>>Return Motorbike</a></li>
                    <li><a href="rentals_current.php" <?php echo $currentPage == 'rentals_current.php' ? 'class="active"' : ''; ?>>My Active Rentals</a></li>
                    <li><a href="rentals_history.php" <?php echo $currentPage == 'rentals_history.php' ? 'class="active"' : ''; ?>>Rental History</a></li>
                <?php endif; ?>
                
                <li><a href="logout.php">Logout (<?php echo Auth::getCurrentUserName(); ?>)</a></li>
            <?php else: ?>
                <!-- Guest menu -->
                <li><a href="index.php" <?php echo $currentPage == 'index.php' ? 'class="active"' : ''; ?>>Home</a></li>
                <li><a href="login.php" <?php echo $currentPage == 'login.php' ? 'class="active"' : ''; ?>>Login</a></li>
                <li><a href="register.php" <?php echo $currentPage == 'register.php' ? 'class="active"' : ''; ?>>Register</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>
