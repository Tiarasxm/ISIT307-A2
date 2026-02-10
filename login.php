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
$email = '';

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
            <a href="index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
    
    <p style="text-align: center; margin-top: 1.5rem;">
        Don't have an account? <a href="register.php" style="color: var(--color-accent); text-decoration: none; font-weight: 500;">Register here</a>
    </p>
</main>

<?php include 'includes/footer.php'; ?>
