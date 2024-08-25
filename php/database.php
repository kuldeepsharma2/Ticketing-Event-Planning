<?php
$servername = "localhost";
$username = "root";
$password = ""; // Set this to your MySQL root password if it has one
$dbname = "eventplannerdb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
