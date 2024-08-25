<?php
include 'database.php';

$event_id = $_GET['event_id'];
$sql = "SELECT * FROM Tickets WHERE event_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

$tickets = array();
while ($row = $result->fetch_assoc()) {
    $tickets[] = $row;
}

echo json_encode($tickets);

$stmt->close();
$conn->close();
?>
