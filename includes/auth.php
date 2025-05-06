
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
 * @param string $password User's password (already hashed)
 * @param string $role User's role (default: 'user')
 * @return bool|int User ID on success, false on failure
 */
function registerUser($name, $email, $password, $role = ROLE_USER) {
    global $conn;
    
    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return false; // Email already exists
    }
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssss", $name, $email, $password, $role);
    
    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        
        // Log activity
        logActivity('register', 'New user registered', $userId);
        
        return $userId;
    }
    
    return false;
}

/**
 * Check if user is logged in
 * 
 * @return bool True if logged in, false otherwise
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check if user is an admin
 * 
 * @return bool True if admin, false otherwise
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === ROLE_ADMIN;
}

/**
 * Require user to be logged in to access page
 * Redirects to login page if not logged in
 */
function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['login_required'] = "Please login to access this page";
        redirect(SITE_URL . '/views/login.php');
    }
}

/**
 * Require user to be an admin to access page
 * Redirects to unauthorized page if not admin
 */
function requireAdmin() {
    requireLogin(); // First check if logged in
    
    if (!isAdmin()) {
        redirect(SITE_URL . '/views/unauthorized.php');
    }
}

/**
 * Logout the current user
 */
function logoutUser() {
    // Log activity before destroying session
    if (isset($_SESSION['user_id'])) {
        logActivity('logout', 'User logged out', $_SESSION['user_id']);
    }
    
    // Destroy session data
    $_SESSION = array();
    
    // Destroy the session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy the session
    session_destroy();
}

/**
 * Reset user password
 * 
 * @param int $userId User ID
 * @param string $newPassword New password (already hashed)
 * @return bool True on success, false on failure
 */
function resetPassword($userId, $newPassword) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $newPassword, $userId);
    
    if ($stmt->execute()) {
        // Log activity
        logActivity('password_reset', 'User password reset', $userId);
        return true;
    }
    
    return false;
}

/**
 * Generate password reset token
 * 
 * @param string $email User's email
 * @return string|bool Reset token or false on failure
 */
function generateResetToken($email) {
    global $conn;
    
    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $userId = $user['id'];
        
        // Generate token
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+24 hours'));
        
        // Delete any existing token for this user
        $stmt = $conn->prepare("DELETE FROM password_resets WHERE user_id = ?");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        
        // Insert new token
        $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $userId, $token, $expires);
        
        if ($stmt->execute()) {
            // Log activity
            logActivity('reset_token', 'Password reset token generated', $userId);
            return $token;
        }
    }
    
    return false;
}

/**
 * Verify password reset token
 * 
 * @param string $token Reset token
 * @return int|bool User ID on success, false on failure
 */
function verifyResetToken($token) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT user_id FROM password_resets WHERE token = ? AND expires_at > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        return $row['user_id'];
    }
    
    return false;
}
