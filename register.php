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
$name = '';
$surname = '';
$phone = '';
$email = '';
$type = '';

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            margin-top: 4rem !important;
            padding: 3rem 2rem !important;
        }
        .index-header {
            background-color: #2C3E50;
            color: white;
            padding: 1.5rem 0;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .index-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .index-header h1 {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 600;
        }
        .header-buttons {
            display: flex;
            gap: 1rem;
        }
        .header-buttons a {
            padding: 0.6rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
        }
        .header-buttons .btn-login {
            background-color: transparent;
            color: white;
            border: 1px solid white;
        }
        .header-buttons .btn-login:hover {
            background-color: white;
            color: #2C3E50;
        }
        .header-buttons .btn-register {
            background-color: #FF9A6C;
            color: white;
            border: 1px solid #FF9A6C;
        }
        .header-buttons .btn-register:hover {
            background-color: #e8855a;
            border-color: #e8855a;
        }
    </style>
</head>
<body>
    <header class="index-header">
        <div class="container">
            <h1><?php echo SITE_NAME; ?></h1>
            <div class="header-buttons">
                <a href="login.php" class="btn-login">Login</a>
                <a href="register.php" class="btn-register">Register</a>
            </div>
        </div>
    </header>

<main class="container narrow">
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
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    
    <p style="text-align: center; margin-top: 1.5rem;">
        Already have an account? <a href="login.php" style="color: var(--color-accent); text-decoration: none; font-weight: 500;">Login here</a>
    </p>
</main>

<?php include 'includes/footer.php'; ?>
