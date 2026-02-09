<?php
/**
 * Register page - User registration
 * Both Administrator and User types can register
 */
require_once 'includes/config.php';
require_once 'includes/validation.php';
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Auth.php';

// Redirect if already logged in
Auth::redirectIfLoggedIn();

$pageTitle = 'Register';
$errors = [];
$success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $name = sanitizeString($_POST['name'] ?? '');
    $surname = sanitizeString($_POST['surname'] ?? '');
    $phone = sanitizeString($_POST['phone'] ?? '');
    $email = sanitizeString($_POST['email'] ?? '');
    $type = sanitizeString($_POST['type'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Validate inputs
    $errors = collectErrors([
        validateRequired($name, 'Name'),
        validateRequired($surname, 'Surname'),
        validatePhone($phone),
        validateEmail($email),
        validateRequired($type, 'User type'),
        validatePassword($password),
        validatePasswordConfirmation($password, $confirmPassword)
    ]);
    
    // Validate user type
    if (!in_array($type, ['Administrator', 'User'])) {
        $errors[] = "Invalid user type";
    }
    
    // If no errors, register user
    if (empty($errors)) {
        $user = new User();
        $user->setName($name);
        $user->setSurname($surname);
        $user->setPhone($phone);
        $user->setEmail($email);
        $user->setType($type);
        $user->setPassword($password);
        
        $result = $user->register();
        
        if ($result === true) {
            $success = true;
            $_SESSION['success_message'] = "Registration successful! Please login.";
            header("Location: login.php");
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
    <h2>Register</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="message error">
            <strong>Please correct the following errors:</strong>
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="surname">Surname *</label>
            <input type="text" id="surname" name="surname" value="<?php echo isset($surname) ? htmlspecialchars($surname) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="phone">Phone *</label>
            <input type="tel" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>" placeholder="e.g., +1234567890" required>
        </div>
        
        <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <label for="type">User Type *</label>
            <select id="type" name="type" required>
                <option value="">-- Select Type --</option>
                <option value="User" <?php echo (isset($type) && $type === 'User') ? 'selected' : ''; ?>>User</option>
                <option value="Administrator" <?php echo (isset($type) && $type === 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="password">Password * (minimum 6 characters)</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label for="confirm_password">Confirm Password *</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn">Register</button>
            <a href="login.php" class="btn btn-secondary">Already have an account? Login</a>
        </div>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
