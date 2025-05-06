<?php
// Initialize session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$loggedIn = isset($_SESSION['user_id']);
$isAdmin = $loggedIn && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
?>

<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="index.php">Luxury Resort & Spa</a>
        </div>
        
        <div class="menu-toggle" id="menu-toggle">
            <i class="fas fa-bars"></i>
        </div>
        
        <div class="nav-links" id="nav-links">
            <a href="index.php">Home</a>
            <a href="views/kamar.php">Rooms</a>
            <a href="views/reservasi.php">Reservation</a>
            <a href="views/cek_reservasi.php">Check Reservation</a>
            
            <?php if ($loggedIn): ?>
                <?php if ($isAdmin): ?>
                    <a href="admin/manage_reservasi.php">Admin Panel</a>
                <?php else: ?>
                    <a href="views/dashboard.php">My Account</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="views/login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
    // Simple toggle for mobile menu
    document.getElementById('menu-toggle').addEventListener('click', function() {
        document.getElementById('nav-links').classList.toggle('active');
    });
</script>
