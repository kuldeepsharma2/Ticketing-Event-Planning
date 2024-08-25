<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'User not authenticated']);
    exit();
}

include './database.php';

$user_id = $_SESSION['user_id'];
$ticket_id = isset($_POST['ticket_id']) ? (int)$_POST['ticket_id'] : 0;

if ($ticket_id > 0) {
    try {
        // Check if the ticket is associated with the current user
        $check_sql = "SELECT COUNT(*) FROM Tickets t JOIN Events e ON t.event_id = e.event_id WHERE t.ticket_id = ? AND e.user_id = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ii", $ticket_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        if ($count > 0) {
            // Temporarily disable foreign key checks
            $conn->query("SET foreign_key_checks = 0");

            // Delete associated purchase history records
            $delete_purchase_sql = "DELETE FROM PurchaseHistory WHERE ticket_id = ?";
            $stmt = $conn->prepare($delete_purchase_sql);
            $stmt->bind_param("i", $ticket_id);
            if (!$stmt->execute()) {
                throw new Exception('Error deleting associated purchase history');
            }
            $stmt->close();

            // Delete associated transactions records
            $delete_transactions_sql = "DELETE FROM Transactions WHERE ticket_id = ?";
            $stmt = $conn->prepare($delete_transactions_sql);
            $stmt->bind_param("i", $ticket_id);
            if (!$stmt->execute()) {
                throw new Exception('Error deleting associated transactions');
            }
            $stmt->close();

            // Perform the delete operation
            $delete_sql = "DELETE FROM Tickets WHERE ticket_id = ?";
            $stmt = $conn->prepare($delete_sql);
            $stmt->bind_param("i", $ticket_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                throw new Exception('Error deleting ticket');
            }
            $stmt->close();

            // Re-enable foreign key checks
            $conn->query("SET foreign_key_checks = 1");
        } else {
            echo json_encode(['success' => false, 'message' => 'Ticket not found or access denied']);
        }
    } catch (Exception $e) {
        // Re-enable foreign key checks in case of an error
        $conn->query("SET foreign_key_checks = 1");
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid ticket ID']);
}

$conn->close();
?>
