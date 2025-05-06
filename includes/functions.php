<?php
/**
 * Common functions for Hotel Reservation System
 */

// Include config file
require_once 'config.php';

/**
 * Sanitize input data
 * 
 * @param string $data The data to sanitize
 * @return string The sanitized data
 */
function sanitize($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    if ($conn) {
        $data = $conn->real_escape_string($data);
    }
    return $data;
}

/**
 * Format date according to site settings
 * 
 * @param string $date The date string
 * @param string $format The format to use (optional)
 * @return string The formatted date
 */
function formatDate($date, $format = DATE_FORMAT) {
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Calculate number of nights between two dates
 * 
 * @param string $checkin Check-in date
 * @param string $checkout Check-out date
 * @return int Number of nights
 */
function calculateNights($checkin, $checkout) {
    $checkinDate = new DateTime($checkin);
    $checkoutDate = new DateTime($checkout);
    $interval = $checkinDate->diff($checkoutDate);
    return $interval->days;
}

/**
 * Calculate total price for reservation
 * 
 * @param float $price Room price per night
 * @param int $nights Number of nights
 * @return float Total price
 */
function calculateTotalPrice($price, $nights) {
    return $price * $nights;
}

/**
 * Generate a random reservation code
 * 
 * @return string Reservation code
 */
function generateReservationCode() {
    return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
}

/**
 * Check if a room is available for the given dates
 * 
 * @param int $roomId Room ID
 * @param string $checkin Check-in date
 * @param string $checkout Check-out date
 * @return bool True if available, false otherwise
 */
function isRoomAvailable($roomId, $checkin, $checkout) {
    global $conn;
    
    // Convert dates to database format
    $checkinDate = date('Y-m-d', strtotime($checkin));
    $checkoutDate = date('Y-m-d', strtotime($checkout));
    
    // Check for overlapping reservations
    $sql = "SELECT COUNT(*) as count FROM reservasi 
            WHERE room_id = ? 
            AND status NOT IN ('cancelled') 
            AND ((check_in <= ? AND check_out > ?) OR (check_in < ? AND check_out >= ?) OR (check_in >= ? AND check_out <= ?))";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $roomId, $checkoutDate, $checkinDate, $checkoutDate, $checkinDate, $checkinDate, $checkoutDate);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    return $row['count'] == 0;
}

/**
 * Get room details by ID
 * 
 * @param int $roomId Room ID
 * @return array|bool Room data or false if not found
 */
function getRoomById($roomId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM kamar WHERE id = ?");
    $stmt->bind_param("i", $roomId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * Get user details by ID
 * 
 * @param int $userId User ID
 * @return array|bool User data or false if not found
 */
function getUserById($userId) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * Get reservation details by reservation code
 * 
 * @param string $code Reservation code
 * @return array|bool Reservation data or false if not found
 */
function getReservationByCode($code) {
    global $conn;
    
    $stmt = $conn->prepare("SELECT r.*, k.nama as room_name, k.harga as room_price 
                           FROM reservasi r 
                           JOIN kamar k ON r.room_id = k.id 
                           WHERE r.kode_reservasi = ?");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    
    return false;
}

/**
 * Update reservation status
 * 
 * @param int $reservationId Reservation ID
 * @param string $status New status
 * @return bool True on success, false on failure
 */
function updateReservationStatus($reservationId, $status) {
    global $conn;
    
    $stmt = $conn->prepare("UPDATE reservasi SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $reservationId);
    
    return $stmt->execute();
}

/**
 * Redirect to a URL
 * 
 * @param string $url The URL to redirect to
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Log activity for auditing
 * 
 * @param string $action The action performed
 * @param string $details Details about the action
 * @param int $userId ID of the user who performed the action (optional)
 */
function logActivity($action, $details, $userId = null) {
    global $conn;
    
    if ($userId === null && isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];
    }
    
    $stmt = $conn->prepare("INSERT INTO activity_log (user_id, action, details, ip_address) VALUES (?, ?, ?, ?)");
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    $stmt->bind_param("isss", $userId, $action, $details, $ipAddress);
    
    return $stmt->execute();
}

/**
 * Pagination function
 * 
 * @param string $table Database table name
 * @param string $whereClause SQL WHERE clause without 'WHERE' (optional)
 * @param int $page Current page number
 * @param int $perPage Items per page
 * @return array Array with total items, total pages, and SQL LIMIT clause
 */
function paginate($table, $whereClause = '', $page = 1, $perPage = ITEMS_PER_PAGE) {
    global $conn;
    
    // Build SQL to count total items
    $countSql = "SELECT COUNT(*) as total FROM $table";
    if (!empty($whereClause)) {
        $countSql .= " WHERE $whereClause";
    }
    
    $result = $conn->query($countSql);
    $row = $result->fetch_assoc();
    $totalItems = $row['total'];
    
    // Calculate total pages
    $totalPages = ceil($totalItems / $perPage);
    
    // Validate current page
    if ($page < 1) {
        $page = 1;
    } elseif ($page > $totalPages && $totalPages > 0) {
        $page = $totalPages;
    }
    
    // Calculate offset for SQL LIMIT
    $offset = ($page - 1) * $perPage;
    $limitClause = "LIMIT $offset, $perPage";
    
    return [
        'totalItems' => $totalItems,
        'totalPages' => $totalPages,
        'currentPage' => $page,
        'perPage' => $perPage,
        'limitClause' => $limitClause
    ];
}
