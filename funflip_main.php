<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit; // Handle preflight request
}

// Include database connection and functions
require 'funflip_db_connection.php';
require 'funflip_functions.php';

// Include database setup
require 'funflip_db_setup.php';

// Check if POST data exists and insert into table
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    // Check if required fields are set
    if (!isset($data['websiteURL']) || !isset($data['websiteTitle']) || !isset($data['category'])) {
        http_response_code(400);
        echo json_encode(array("error" => "Required fields (websiteURL, websiteTitle, category) are missing."));
        exit;
    }

    // Sanitize input
    $websiteURL = sanitizeInput($conn, $data['websiteURL']);
    $websiteTitle = sanitizeInput($conn, $data['websiteTitle']);
    $category = sanitizeInput($conn, $data['category']);

    // Generate or retrieve user ID
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_id = getUserIDFromCookie() ?? generateUserID($ip_address);
    setUserIDCookie($user_id);

    // Convert user_id to MD5 hash
    $user_id_md5 = md5($user_id);

    // Insert data into mastertable
    $sql_insert_master = "INSERT INTO mastertable (websiteURL, websiteTitle, category)
                          VALUES ('$websiteURL', '$websiteTitle', '$category')";
    if ($conn->query($sql_insert_master) !== TRUE) {
        http_response_code(500);
        echo json_encode(array("error" => "Error inserting into mastertable: " . $conn->error));
        exit;
    }

    // Get the inserted ID
    $master_id = $conn->insert_id;

    // Insert data into funfliptable
    $sql_insert_funflip = "INSERT INTO funfliptable (id, websiteURL, websiteTitle, category, user_id)
                           VALUES ('$master_id', '$websiteURL', '$websiteTitle', '$category', '$user_id_md5')";
    if ($conn->query($sql_insert_funflip) !== TRUE) {
        http_response_code(500);
        echo json_encode(array("error" => "Error inserting into funfliptable: " . $conn->error));
        exit;
    }

    // Respond with success message
    http_response_code(200);
    echo json_encode(array("status" => "success", "message" => "Data inserted successfully."));
}

// Fetch top 5 elements from funfliptable in descending order of date_time
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $sql_fetch_top5 = "SELECT * FROM funfliptable ORDER BY date_time DESC LIMIT 5";

    $result = $conn->query($sql_fetch_top5);
    if ($result->num_rows > 0) {
        $data = array();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        header('Content-Type: application/json');
        echo json_encode(array("message" => "No data found."));
    }
}

// Close connection
$conn->close();
?>
