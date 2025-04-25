<?php
$host = "localhost";
$user = "root";  // Change if using a different user
$pass = "";  // Change if you set a password
$db = "supermarket_db";

$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
