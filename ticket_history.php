<?php include './php/check_login.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include './php/database.php';
$user_id = $_SESSION['user_id'];

$sql = "SELECT ph.purchase_id, ph.ticket_id, ph.quantity, ph.total_amount, t.ticket_type, t.price, e.event_name 
        FROM PurchaseHistory ph 
        JOIN Tickets t ON ph.ticket_id = t.ticket_id 
        JOIN Events e ON t.event_id = e.event_id 
        WHERE ph.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
</head>
<body>
<div class="wrapper">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <a class="navbar-brand" href="#">Event Planner</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="./dashboard.php">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./ticket_history.php">Purchase History <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Add Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./dashboardticket.php">Ticket Management</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./ticket.php">Ticket</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-danger" href="./php/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <div class="container mt-5">
        <h1>Purchase History</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Purchase ID</th>
                    <th>Event Name</th>
                    <th>Ticket Type</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row['purchase_id']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['ticket_type']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['quantity']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['price']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['total_amount']) . "</td>";
                    echo "<td><a href='invoicehistory.php?purchase_id=" . htmlspecialchars($row['purchase_id']) . "' class='btn btn-primary'>View Invoice</a></td>";
                    echo "</tr>";
                }
                $stmt->close();
                $conn->close();
                ?>
            </tbody>
        </table>
    </div></div>
    

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
