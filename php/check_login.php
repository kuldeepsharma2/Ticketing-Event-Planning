<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['loggedin']) || !$_SESSION['loggedin']) {
    // User is not logged in, redirect to login page
    header("Location: http://localhost/webdevelopment/index.html");
    exit(); // Ensure no further code is executed after redirection
}
?>

