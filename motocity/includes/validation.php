<?php
/**
 * Validation helper functions
 * Server-side validation for all user inputs
 */

/**
 * Validate required field (not empty)
 * @param string $value
 * @param string $fieldName
 * @return string|null Error message or null if valid
 */
function validateRequired($value, $fieldName) {
    if (empty(trim($value))) {
        return "$fieldName is required";
    }
    return null;
}

/**
 * Validate email format
 * @param string $email
 * @return string|null Error message or null if valid
 */
function validateEmail($email) {
    if (empty(trim($email))) {
        return "Email is required";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "Invalid email format";
    }
    return null;
}

/**
 * Validate phone format (basic - digits, spaces, dashes, parentheses)
 * @param string $phone
 * @return string|null Error message or null if valid
 */
function validatePhone($phone) {
    if (empty(trim($phone))) {
        return "Phone is required";
    }
    // Allow digits, spaces, dashes, parentheses, and plus sign
    if (!preg_match('/^[\d\s\-\(\)\+]+$/', $phone)) {
        return "Invalid phone format";
    }
    // Check minimum length (at least 8 digits)
    $digitsOnly = preg_replace('/\D/', '', $phone);
    if (strlen($digitsOnly) < 8) {
        return "Phone must contain at least 8 digits";
    }
    return null;
}

/**
 * Validate positive number
 * @param mixed $value
 * @param string $fieldName
 * @return string|null Error message or null if valid
 */
function validatePositiveNumber($value, $fieldName) {
    if (empty(trim($value))) {
        return "$fieldName is required";
    }
    if (!is_numeric($value) || $value <= 0) {
        return "$fieldName must be a positive number";
    }
    return null;
}

/**
 * Validate datetime format
 * @param string $datetime
 * @param string $fieldName
 * @return string|null Error message or null if valid
 */
function validateDateTime($datetime, $fieldName) {
    if (empty(trim($datetime))) {
        return "$fieldName is required";
    }
    // Try to parse the datetime
    $date = DateTime::createFromFormat('Y-m-d\TH:i', $datetime);
    if (!$date) {
        // Try alternative format
        $date = DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
    }
    if (!$date) {
        return "Invalid $fieldName format";
    }
    return null;
}

/**
 * Validate password strength (minimum 6 characters)
 * @param string $password
 * @return string|null Error message or null if valid
 */
function validatePassword($password) {
    if (empty($password)) {
        return "Password is required";
    }
    if (strlen($password) < 6) {
        return "Password must be at least 6 characters long";
    }
    return null;
}

/**
 * Validate password confirmation
 * @param string $password
 * @param string $confirmPassword
 * @return string|null Error message or null if valid
 */
function validatePasswordConfirmation($password, $confirmPassword) {
    if ($password !== $confirmPassword) {
        return "Passwords do not match";
    }
    return null;
}

/**
 * Sanitize string input
 * @param string $value
 * @return string
 */
function sanitizeString($value) {
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate and collect all errors
 * @param array $validations Array of validation results
 * @return array Array of error messages
 */
function collectErrors($validations) {
    $errors = [];
    foreach ($validations as $error) {
        if ($error !== null) {
            $errors[] = $error;
        }
    }
    return $errors;
}
?>
