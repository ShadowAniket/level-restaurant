<?php
// Database configuration
$host = 'localhost';
$db   = 'lvl_restro';  // Updated database name with underscore
$user = 'root';
$pass = '';  // Update with your database password if needed

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>