<?php
session_start();
$errors = [];
$name = "";
$surname = "";
$phone = "";
$email = "";
$type = "";

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connect.php';
    
    $name = mysqli_real_escape_string($conn, trim($_POST['name']));
    $surname = mysqli_real_escape_string($conn, trim($_POST['surname']));
    $phone = '+65' . mysqli_real_escape_string($conn, trim($_POST['phone']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $type = mysqli_real_escape_string($conn, $_POST['type']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($name)) $errors[] = "Name is required";
    if (empty($surname)) $errors[] = "Surname is required";
    if (empty($_POST['phone']) || !preg_match('/^[0-9]{8}$/', $_POST['phone'])) {
        $errors[] = "Phone must be 8 digits";
    }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    if (empty($type) || !in_array($type, ['Administrator', 'User'])) {
        $errors[] = "Valid user type is required";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email exists
    if (empty($errors)) {
        $sql = "SELECT id FROM users WHERE email = '$email'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $errors[] = "Email already registered";
        }
    }
    
    // Insert user
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, surname, phone, email, type, password) 
                VALUES ('$name', '$surname', '$phone', '$email', '$type', '$password_hash')";
        
        if ($conn->query($sql) === TRUE) {
            $_SESSION['success_message'] = "Registration successful! Please login.";
            header("Location: login.php");
            exit();
        } else {
            $errors[] = "Registration failed: " . $conn->error;
        }
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MotoCity</title>
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
        .container.narrow {
            max-width: 600px;
        }
    </style>
</head>
<body>
    <header class="index-header">
        <div class="container">
            <h1>MotoCity</h1>
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
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="surname">Surname *</label>
                <input type="text" id="surname" name="surname" value="<?php echo htmlspecialchars($surname); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="phone">Phone *</label>
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <span style="padding: 0.75rem; background-color: var(--color-light); border: 1px solid #ddd; border-radius: 4px; font-weight: 500;">+65</span>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars(str_replace('+65', '', $phone)); ?>" placeholder="e.g., 91234568" pattern="[0-9]{8}" maxlength="8" required style="flex: 1;">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="type">User Type *</label>
                <select id="type" name="type" required>
                    <option value="">-- Select Type --</option>
                    <option value="User" <?php echo ($type === 'User') ? 'selected' : ''; ?>>User</option>
                    <option value="Administrator" <?php echo ($type === 'Administrator') ? 'selected' : ''; ?>>Administrator</option>
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
</body>
</html>
