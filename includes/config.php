<?php
/**
 * Configuration file for Hotel Reservation System
 * Contains database connection details and site settings
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');         // Change to your database username
define('DB_PASS', '');             // Change to your database password
define('DB_NAME', 'hotel_db');     // Change to your database name

// Site configuration
define('SITE_NAME', 'Luxury Resort & Spa');
define('SITE_URL', 'http://localhost/hotel-reservation-system'); // Change to your site URL

// Email configuration (for reservation confirmations, etc.)
define('EMAIL_FROM', 'noreply@luxuryresort.com');
define('EMAIL_NAME', 'Luxury Resort & Spa');

// Pagination settings
define('ITEMS_PER_PAGE', 9); // Number of rooms to display per page

// Date format settings
define('DATE_FORMAT', 'Y-m-d');
define('DATETIME_FORMAT', 'Y-m-d H:i:s');

// Reservation status options
define('STATUS_PENDING', 'pending');
define('STATUS_CONFIRMED', 'confirmed');
define('STATUS_CHECKED_IN', 'checked_in');
define('STATUS_CHECKED_OUT', 'checked_out');
define('STATUS_CANCELLED', 'cancelled');

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_USER', 'user');

// File upload paths
define('UPLOAD_PATH', '../assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Connect to the database
function connectDB() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check for connection errors
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set character set
    $conn->set_charset('utf8mb4');
    
    return $conn;
}

// Initialize database connection
$conn = connectDB();
