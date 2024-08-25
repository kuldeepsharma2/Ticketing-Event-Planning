<?php include './php/check_login.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include './php/database.php';

$purchase_id = $_GET['purchase_id'] ?? null;
if (!$purchase_id) {
    echo "Purchase ID is missing.";
    exit();
}

$sql = "SELECT ph.purchase_id, ph.total_amount, ph.quantity, t.ticket_type, t.price, u.username, e.event_name
        FROM PurchaseHistory ph
        JOIN Tickets t ON ph.ticket_id = t.ticket_id
        JOIN Users u ON ph.user_id = u.user_id
        JOIN Events e ON t.event_id = e.event_id
        WHERE ph.purchase_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $purchase_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No such purchase found.";
    $stmt->close();
    $conn->close();
    exit();
}

$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
    <link rel="stylesheet" href="./css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1>Invoice</h1>
    <table class="table table-bordered">
        <tr>
            <th>Purchase ID</th>
            <td><?php echo htmlspecialchars($row['purchase_id']); ?></td>
        </tr>
        <tr>
            <th>User</th>
            <td><?php echo htmlspecialchars($row['username']); ?></td>
        </tr>
        <tr>
            <th>Event Name</th>
            <td><?php echo htmlspecialchars($row['event_name']); ?></td>
        </tr>
        <tr>
            <th>Ticket Type</th>
            <td><?php echo htmlspecialchars($row['ticket_type']); ?></td>
        </tr>
        <tr>
            <th>Price per Ticket</th>
            <td><?php echo htmlspecialchars($row['price']); ?></td>
        </tr>
        <tr>
            <th>Quantity</th>
            <td><?php echo htmlspecialchars($row['quantity']); ?></td>
        </tr>
        <tr>
            <th>Total Amount</th>
            <td><?php echo htmlspecialchars($row['total_amount']); ?></td>
        </tr>
    </table>
    <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
</div>
<footer class="footer bg-dark text-white text-center py-3 mt-4" style="padding: 1rem;text-align: center;margin-top: auto;margin-top: 0px !important;left: 0;bottom: 0;width: 100%;">
        <p>&copy; 2024 Shivam. All rights reserved.</p>
    </footer></div>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
