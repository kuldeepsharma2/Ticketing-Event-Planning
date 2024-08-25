<?php
// Include the database connection file
require 'database.php';

// Start the session
session_start();

// Set the response header
header('Content-Type: application/json');

// Initialize the response array
$response = ['status' => 'error', 'message' => 'An unknown error occurred.'];

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['loggedin']) {
    $response = ['status' => 'error', 'message' => 'User not logged in.'];
    echo json_encode($response);
    exit();
}

// Get the logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO Events (user_id, event_name, event_date, event_time, location, description) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $event_name, $event_date, $event_time, $location, $description);

    // Execute the statement
    if ($stmt->execute()) {
        // Event creation successful
        $response = ['status' => 'success', 'message' => 'Event created successfully.'];
    } else {
        $response = ['status' => 'error', 'message' => 'Error: ' . $stmt->error];
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();

// Send the response as JSON
echo json_encode($response);
?>
