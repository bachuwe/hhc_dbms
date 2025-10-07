<?php
$servername = "sql205.infinityfree.com";  // Database host
$username = "if0_38624283"; // Your database username
$password = "q3alw91v1"; // Your database password
$dbname = "if0_38624283_hhctak"; // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

