<?php
// Database connection variables
$servername = "localhost";
$username = "funflip_user";  // Your MySQL username
$password = "123456";        // Your MySQL password
$database = "thefunandflip_db";
$port = 3306; // Your MySQL port number

// Create connection
$conn = new mysqli($servername, $username, $password, $database, $port);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
