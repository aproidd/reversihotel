<?php
/**
 * Registration page for Hotel Reservation System
 */
$pageTitle = "Register";
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    redirect(SITE_URL . '/index.php');
}

// Set error/success message if exists in session
$errorMsg = '';
$successMsg = '';

if (isset($_SESSION['register_error'])) {
    $errorMsg = $_SESSION['register_error'];
    unset($_SESSION['register_error']);
}

if (isset($_SESSION['register_success'])) {
    $successMsg = $_SESSION['register_success'];
    unset($_SESSION['register_success']);
}

// Include header
include_once '../components/header.php';
?>

<div class="register-container">
    <div class="register-form-container">
        <h1>Create an Account</h1>
        
        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success">
                <?php echo $successMsg; ?>
            </div>
        <?php endif; ?>
        
        <form action="../proses/proses_register.php" method="post" class="register-form">
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required class="form-control">
                <small class="form-text text-muted">Password must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, and one number.</small>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
            
            <div class="form-links">
                <p>Already have an account? <a href="login.php">Login</a></p>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
include_once '../components/footer.php';
?>
