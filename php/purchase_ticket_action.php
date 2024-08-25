<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'database.php';

    $user_id = $_SESSION['user_id'];
    $ticket_id = $_POST['ticket_id'];
    $quantity = $_POST['quantity'];

    // Check ticket availability
    $sql = "SELECT quantity, price FROM Tickets WHERE ticket_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $ticket_id);
    $stmt->execute();
    $stmt->bind_result($available_quantity, $price);
    $stmt->fetch();
    $stmt->close();

    if ($available_quantity >= $quantity) {
        $total_amount = $price * $quantity;

        // Create transaction
        $sql = "INSERT INTO Transactions (user_id, ticket_id, quantity, total_amount) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $user_id, $ticket_id, $quantity, $total_amount);
        $stmt->execute();
        $transaction_id = $stmt->insert_id;
        $stmt->close();

        // Update ticket quantity
        $sql = "UPDATE Tickets SET quantity = quantity - ? WHERE ticket_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $quantity, $ticket_id);
        $stmt->execute();
        $stmt->close();

        // Insert into PurchaseHistory
        $sql = "INSERT INTO PurchaseHistory (user_id, ticket_id, quantity, total_amount) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiid", $user_id, $ticket_id, $quantity, $total_amount);
        $stmt->execute();
        $stmt->close();

        header("Location: ../invoice.php?transaction_id=$transaction_id");
        exit();
    } else {
        echo "Not enough tickets available.";
    }

    $conn->close();
}
?>
