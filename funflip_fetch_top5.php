<?php
// Display all errors for debugging purposes
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow access from all origins
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

// Include the database connection
require 'funflip_db_connection.php';

// Function to sanitize input data
function sanitizeInput($conn, $data) {
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}

// Fetch top 5 elements from funfliptable in descending order of date_time
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Query to fetch the top 5 records
    $sql_fetch_top5 = "SELECT websiteTitle, websiteURL, date_time FROM funfliptable ORDER BY date_time DESC LIMIT 5";

    // Execute the query
    $result = $conn->query($sql_fetch_top5);
    
    // Check if there are any results
    if ($result->num_rows > 0) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        // Send the result as a JSON response
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        // Send a response indicating no data found
        header('Content-Type: application/json');
        echo json_encode(array("message" => "No data found."));
    }
}

// Close the database connection
$conn->close();
?>
