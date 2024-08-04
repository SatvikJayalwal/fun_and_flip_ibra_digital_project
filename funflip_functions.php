<?php
date_default_timezone_set('Asia/Kolkata'); // Set timezone to Indian Standard Time

// Function to generate unique user ID
function generateUserID($ip_address) {
    $datetime = date('Y-m-d_H:i:s');
    $salt = "ibra_digital_branding_services"; // Change this to a more secure salt
    return ($ip_address . '_' . $datetime . '_' . $salt);
}

// Function to set a cookie with the generated user ID
function setUserIDCookie($user_id) {
    // Set cookie for 5 years
    setcookie('user_id', $user_id, time() + (86400 * 365 * 5), "/");
}

// Function to retrieve user ID from cookie
function getUserIDFromCookie() {
    return $_COOKIE['user_id'] ?? null;
}

// Sanitize input data
function sanitizeInput($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
?>
