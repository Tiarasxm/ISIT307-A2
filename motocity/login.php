<?php
/**
 * Login page - User authentication
 */
require_once 'includes/config.php';
require_once 'includes/validation.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';

// Redirect if already logged in
Auth::redirectIfLoggedIn();

$pageTitle = 'Login';
$errors = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $email = sanitizeString($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate inputs
    $errors = collectErrors([
        validateEmail($email),
        validateRequired($password, 'Password')
    ]);
    
    // If no errors, attempt login
    if (empty($errors)) {
        $user = new User();
        $result = $user->login($email, $password);
        
        if ($result instanceof User) {
            // Login successful - set session
            Auth::setUserSession(
                $result->getId(),
                $result->getName(),
                $result->getSurname(),
                $result->getEmail(),
                $result->getType()
            );
            
            // Redirect to dashboard
            header("Location: dashboard.php");
            exit();
        } else {
            $errors[] = $result;
        }
    }
}

include 'includes/header.php';
include 'includes/nav.php';
?>

<main class="container">
    <h2>Login</h2>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="message success">
            <?php 
            echo htmlspecialchars($_SESSION['success_message']); 
            unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <?php if (!empty($errors)): ?>
        <div class="message error">
            <strong>Login failed:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="password">Password *</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Login</button>
            <a href="register.php" class="btn btn-secondary">Don't have an account? Register</a>
        </div>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
