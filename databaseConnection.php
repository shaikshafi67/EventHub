<?php
$host = "localhost";
$user = "root";
$password = ""; // or your MySQL password
$dbname = "EventManagement";

// Create connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>