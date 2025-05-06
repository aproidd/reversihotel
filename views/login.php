<?php
/**
 * Login page for Hotel Reservation System
 */
$pageTitle = "Login";
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    redirect(SITE_URL . '/index.php');
}

// Set error message if exists in session
$errorMsg = '';
if (isset($_SESSION['login_error'])) {
    $errorMsg = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}

// Include header
include_once '../components/header.php';
?>

<div class="login-container">
    <div class="login-form-container">
        <h1>Login to <?php echo SITE_NAME; ?></h1>
        
        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger">
                <?php echo $errorMsg; ?>
            </div>
        <?php endif; ?>
        
        <form action="../proses/proses_login.php" method="post" class="login-form">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required class="form-control">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
            
            <div class="form-links">
                <p>Don't have an account? <a href="register.php">Register</a></p>
                <p><a href="forgot_password.php">Forgot Password?</a></p>
            </div>
        </form>
    </div>
</div>

<?php
// Include footer
include_once '../components/footer.php';
?>
