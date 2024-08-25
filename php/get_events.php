<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'database.php';
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['loggedin']) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT e.event_id, e.event_name, e.event_date, e.event_time, e.location, e.description, u.username, e.user_id AS event_user_id 
        FROM Events e 
        JOIN Users u ON e.user_id = u.user_id";
$result = $conn->query($sql);

if (!$result) {
    echo json_encode(['error' => 'Database query failed']);
    exit();
}

$events = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['is_owner'] = ($row['event_user_id'] == $user_id);
        $events[] = $row;
    }
}

echo json_encode(['user_id' => $user_id, 'events' => $events]);

$conn->close();
?>
