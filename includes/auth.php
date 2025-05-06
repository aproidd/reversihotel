<?php
/**
 * Authentication functions for Hotel Reservation System
 */

// Include config if not already included
if (!function_exists('connectDB')) {
    require_once 'config.php';
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Attempt to login a user
 * 
 * @param string $email User's email
 * @param string $password User's password
 * @return bool|array User data on success, false on failure
 */
function loginUser($email, $password) {
    global $conn;
    
    // Prepare SQL statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            // Password is correct, create session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nama'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Update last login time
            $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
            $updateStmt->bind_param("i", $user['id']);
            $updateStmt->execute();
            
            // Log activity
            logActivity('login', 'User logged in', $user['id']);
            
            return $user;
        }
    }
    
    return false;
}

/**
 * Register a new user
 * 
 * @param string $name User's name
 * @param string $email User's email
 * @param string $password User's password
 * @param string $role User's role (default: 'user')
 * @return bool|int User ID on success, false on failure
 */
function registerUser($name, $email, $password, $role = ROLE_USER) {
    global $conn;
    
    // Check if email
