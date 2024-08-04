<?php
// Include database connection
require 'funflip_db_connection.php';

// Create database if it doesn't exist
$sql_create_db = "CREATE DATABASE IF NOT EXISTS $database";
if ($conn->query($sql_create_db) !== TRUE) {
    die("Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db($database);

// Create mastertable if not exists
$sql_create_mastertable = "CREATE TABLE IF NOT EXISTS mastertable (
    id INT AUTO_INCREMENT PRIMARY KEY,
    websiteURL VARCHAR(255) NOT NULL,
    websiteTitle VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    flag TINYINT DEFAULT 1
)";
if ($conn->query($sql_create_mastertable) !== TRUE) {
    die("Error creating mastertable: " . $conn->error);
}

// Create funfliptable if not exists
$sql_create_funfliptable = "CREATE TABLE IF NOT EXISTS funfliptable (
    id INT PRIMARY KEY,
    websiteURL VARCHAR(255) NOT NULL,
    websiteTitle VARCHAR(255) NOT NULL,
    category VARCHAR(255) NOT NULL,
    date_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,    
    user_id VARCHAR(255),
    flag TINYINT DEFAULT 1,
    FOREIGN KEY (id) REFERENCES mastertable(id)
)";
if ($conn->query($sql_create_funfliptable) !== TRUE) {
    die("Error creating funfliptable: " . $conn->error);
}
?>
