<?php
/**
 * Process login form submission
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $email = sanitize($_POST['email']);
    $password = $_POST['password']; // Don't sanitize password before verification
    
    // Validate input
    $errors = [];
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    }
    
    // If no validation errors, attempt login
    if (empty($errors)) {
        $user = loginUser($email, $password);
        
        if ($user) {
            // Login successful, redirect based on user role
            if ($user['role'] == ROLE_ADMIN) {
                redirect(SITE_URL . '/admin/dashboard.php');
            } else {
                redirect(SITE_URL . '/index.php');
            }
        } else {
            // Login failed
            $_SESSION['login_error'] = "Invalid email or password";
            redirect(SITE_URL . '/views/login.php');
        }
    } else {
        // Store errors in session and redirect back
        $_SESSION['login_error'] = implode("<br>", $errors);
        redirect(SITE_URL . '/views/login.php');
    }
} else {
    // If not a POST request, redirect to login page
    redirect(SITE_URL . '/views/login.php');
}
?>
