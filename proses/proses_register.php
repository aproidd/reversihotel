<?php
/**
 * Process registration form submission
 */
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password']; // Don't sanitize password before hashing
    $confirm_password = $_POST['confirm_password'];
    
    // Validate input
    $errors = [];
    
    if (empty($name)) {
        $errors[] = "Name is required";
    }
    
    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }
    
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif (!preg_match('/[A-Z]/', $password) || 
              !preg_match('/[a-z]/', $password) || 
              !preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must include at least one uppercase letter, one lowercase letter, and one number";
    }
    
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }
    
    // Check if email already exists
    global $conn;
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email already registered";
    }
    
    // If no validation errors, register user
    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Register user
        $userId = registerUser($name, $email, $hashed_password);
        
        if ($userId) {
            // Registration successful
            $_SESSION['register_success'] = "Registration successful! You can now login.";
            redirect(SITE_URL . '/views/login.php');
        } else {
            // Registration failed
            $_SESSION['register_error'] = "Registration failed. Please try again.";
            redirect(SITE_URL . '/views/register.php');
        }
    } else {
        // Store errors in session and redirect back
        $_SESSION['register_error'] = implode("<br>", $errors);
        redirect(SITE_URL . '/views/register.php');
    }
} else {
    // If not a POST request, redirect to registration page
    redirect(SITE_URL . '/views/register.php');
}
?>
