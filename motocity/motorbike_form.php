<?php
/**
 * Motorbike Form page - Insert/Edit motorbikes (Admin only)
 */
require_once 'includes/config.php';
require_once 'includes/validation.php';
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/Motorbike.php';

// Require admin access
Auth::requireAdmin();

$pageTitle = 'Motorbike Form';
$errors = [];
$success = false;
$isEdit = false;
$oldCode = null;

$motorbike = new Motorbike();

// Check if editing existing motorbike
if (isset($_GET['code'])) {
    $isEdit = true;
    $oldCode = $_GET['code'];
    $existing = $motorbike->getMotorbikeByCode($oldCode);
    
    if (!$existing) {
        $_SESSION['error_message'] = "Motorbike not found";
        header("Location: motorbikes_list.php");
        exit();
    }
    
    // Pre-fill form with existing data
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $code = $existing->getCode();
        $location = $existing->getRentingLocation();
        $description = $existing->getDescription();
        $costPerHour = $existing->getCostPerHour();
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $code = sanitizeString($_POST['code'] ?? '');
    $location = sanitizeString($_POST['location'] ?? '');
    $description = sanitizeString($_POST['description'] ?? '');
    $costPerHour = sanitizeString($_POST['cost_per_hour'] ?? '');
    
    // Validate inputs
    $errors = collectErrors([
        validateRequired($code, 'Code'),
        validateRequired($location, 'Renting Location'),
        validateRequired($description, 'Description'),
        validatePositiveNumber($costPerHour, 'Cost per Hour')
    ]);
    
    // If no errors, insert or update
    if (empty($errors)) {
        $bikeObj = new Motorbike();
        $bikeObj->setCode($code);
        $bikeObj->setRentingLocation($location);
        $bikeObj->setDescription($description);
        $bikeObj->setCostPerHour($costPerHour);
        
        if ($isEdit) {
            $result = $bikeObj->update($oldCode);
        } else {
            $result = $bikeObj->insert();
        }
        
        if ($result === true) {
            $success = true;
            $_SESSION['success_message'] = $isEdit ? "Motorbike updated successfully" : "Motorbike added successfully";
            header("Location: motorbikes_list.php");
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
    <h2><?php echo $isEdit ? 'Edit Motorbike' : 'Add New Motorbike'; ?></h2>
    
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
    
    <form method="POST" action="motorbike_form.php<?php echo $isEdit ? '?code=' . urlencode($oldCode) : ''; ?>">
        <div class="form-group">
            <label for="code">Motorbike Code *</label>
            <input type="text" id="code" name="code" value="<?php echo isset($code) ? htmlspecialchars($code) : ''; ?>" 
                   <?php echo $isEdit ? 'readonly' : ''; ?> required>
            <?php if ($isEdit): ?>
                <small>Code cannot be changed when editing</small>
            <?php endif; ?>
        </div>
        
        <div class="form-group">
            <label for="location">Renting Location *</label>
            <input type="text" id="location" name="location" value="<?php echo isset($location) ? htmlspecialchars($location) : ''; ?>" 
                   placeholder="e.g., Downtown, Airport, City Center" required>
        </div>
        
        <div class="form-group">
            <label for="description">Description *</label>
            <textarea id="description" name="description" required><?php echo isset($description) ? htmlspecialchars($description) : ''; ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="cost_per_hour">Cost per Hour ($) *</label>
            <input type="number" id="cost_per_hour" name="cost_per_hour" step="0.01" min="0.01" 
                   value="<?php echo isset($costPerHour) ? htmlspecialchars($costPerHour) : ''; ?>" required>
        </div>
        
        <div class="form-group">
            <button type="submit" class="btn"><?php echo $isEdit ? 'Update Motorbike' : 'Add Motorbike'; ?></button>
            <a href="motorbikes_list.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</main>

<?php include 'includes/footer.php'; ?>
