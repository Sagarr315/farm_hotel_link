<?php
// Database configuration
$servername = "localhost"; // Usually "localhost"
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "farm_hotel_link"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: Set the character set to utf8
$conn->set_charset("utf8");
?>