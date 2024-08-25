<?php
require 'database.php';
session_start();

header('Content-Type: application/json'); // Ensure content type is JSON

if (isset($_POST['event_id'])) {
    $event_id = $_POST['event_id'];
    $user_id = $_SESSION['user_id']; // Assuming user ID is stored in session

    // Verify if the user is the owner of the event
    $verify_owner_query = "SELECT user_id FROM events WHERE event_id = ?";
    if ($stmt = mysqli_prepare($conn, $verify_owner_query)) {
        mysqli_stmt_bind_param($stmt, 'i', $event_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $event_user_id);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to prepare statement for verifying event owner.']);
        mysqli_close($conn);
        exit;
    }

    if ($event_user_id === $user_id) {
        // Start a transaction
        mysqli_begin_transaction($conn);

        try {
            // Temporarily disable foreign key checks
            mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 0");

            // Delete the event
            $delete_event_query = "DELETE FROM events WHERE event_id = ?";
            if ($stmt = mysqli_prepare($conn, $delete_event_query)) {
                mysqli_stmt_bind_param($stmt, 'i', $event_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            } else {
                throw new Exception('Failed to prepare statement for deleting event.');
            }

            // Re-enable foreign key checks
            mysqli_query($conn, "SET FOREIGN_KEY_CHECKS = 1");

            // Commit the transaction
            mysqli_commit($conn);

            echo json_encode(['success' => true, 'message' => 'Event deleted successfully.']);
        } catch (Exception $e) {
            // Rollback the transaction in case of error
            mysqli_rollback($conn);
            echo json_encode(['success' => false, 'message' => 'Error deleting event: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'You are not authorized to delete this event.']);
    }

    mysqli_close($conn);
} else {
    echo json_encode(['success' => false, 'message' => 'Event ID not provided.']);
}
?>
