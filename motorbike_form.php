<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';

Auth::requireAdmin();

$errors = [];
$success = '';
$isEdit = false;
$code = '';
$location = '';
$description = '';
$cost = '';

// Check if editing
if (isset($_GET['code'])) {
    $isEdit = true;
    $motorbike = new Motorbike();
    $motoData = $motorbike->getByCode($_GET['code']);
    
    if ($motoData) {
        $code = $motoData->getCode();
        $location = $motoData->getRentingLocation();
        $description = $motoData->getDescription();
        $cost = $motoData->getCostPerHour();
    } else {
        $errors[] = "Motorbike not found";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $cost = trim($_POST['cost'] ?? '');
    
    // Validation
    if (empty($code)) $errors[] = "Code is required";
    if (empty($location)) $errors[] = "Location is required";
    if (empty($description)) $errors[] = "Description is required";
    if (empty($cost) || !is_numeric($cost) || $cost <= 0) {
        $errors[] = "Valid cost per hour is required";
    }
    
    if (empty($errors)) {
        $motorbike = new Motorbike();
        $motorbike->setCode($code);
        $motorbike->setRentingLocation($location);
        $motorbike->setDescription($description);
        $motorbike->setCostPerHour($cost);
        
        if ($isEdit) {
            $result = $motorbike->update();
        } else {
            $result = $motorbike->create();
        }
        
        if ($result === true) {
            $success = $isEdit ? "Motorbike updated successfully!" : "Motorbike added successfully!";
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
    <title><?php echo $isEdit ? 'Edit' : 'Add'; ?> Motorbike - MotoCity</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        main.container {
            max-width: 800px;
            padding: 2.5rem 2rem;
            margin-top: 4rem;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/nav.php'; ?>

    <main class="container">
        <h2><?php echo $isEdit ? 'Edit' : 'Add New'; ?> Motorbike</h2>
        
        <?php if ($success): ?>
            <div class="message success">
                <?php echo htmlspecialchars($success); ?>
            </div>
            <div class="mt-2">
                <a href="motorbike_form.php" class="btn">Add Another</a>
                <a href="motorbikes_list.php" class="btn btn-secondary">View All Motorbikes</a>
            </div>
        <?php else: ?>
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
            
            <form method="POST" action="motorbike_form.php<?php echo $isEdit ? '?code=' . urlencode($code) : ''; ?>">
                <div class="form-group">
                    <label for="code">Motorbike Code *</label>
                    <input type="text" id="code" name="code" value="<?php echo htmlspecialchars($code); ?>" 
                           <?php echo $isEdit ? 'readonly' : ''; ?> required>
                    <?php if ($isEdit): ?>
                        <small>Code cannot be changed when editing</small>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="location">Renting Location *</label>
                    <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($location); ?>" 
                           placeholder="e.g., Orchard MRT Station" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="4" required><?php echo htmlspecialchars($description); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="cost">Cost per Hour ($) *</label>
                    <input type="number" id="cost" name="cost" value="<?php echo htmlspecialchars($cost); ?>" 
                           step="0.01" min="0.01" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn"><?php echo $isEdit ? 'Update' : 'Add'; ?> Motorbike</button>
                    <a href="motorbikes_list.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </main>
</body>
</html>
