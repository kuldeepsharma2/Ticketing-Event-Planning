<?php
session_start();
include './database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket_id = $_POST['ticket_id'];
    $ticket_type = $_POST['ticket_type'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    if ($ticket_id && $ticket_type && $price && $quantity) {
        $sql = "UPDATE Tickets SET ticket_type = ?, price = ?, quantity = ? WHERE ticket_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sdii", $ticket_type, $price, $quantity, $ticket_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update ticket.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
$conn->close();
?>
