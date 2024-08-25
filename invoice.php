<?php include './php/check_login.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['transaction_id'])) {
    header("Location: login.php");
    exit();
}

include './php/database.php';
$transaction_id = $_GET['transaction_id'];
$user_id = $_SESSION['user_id'];

$sql = "SELECT t.*, e.event_name, e.event_date, e.event_time, e.location, tk.ticket_type 
        FROM Transactions t 
        JOIN Tickets tk ON t.ticket_id = tk.ticket_id
        JOIN Events e ON tk.event_id = e.event_id
        WHERE t.transaction_id = ? AND t.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $transaction_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$transaction = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$transaction) {
    echo "<p>Transaction not found or you do not have permission to view this transaction.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
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
                        <li class="nav-item ">
                            <a class="nav-link" href="./ticket_history.php">Purchace History <span class="sr-only">(current)</span></a>
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
                            <a class=" btn btn-danger" href="./php/logout.php">Logout</a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>
    <div class="container mt-5">
        <h1>Invoice</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Transaction ID: <?php echo htmlspecialchars($transaction['transaction_id']); ?></h5>
                <p class="card-text"><strong>Event Name:</strong> <?php echo htmlspecialchars($transaction['event_name']); ?></p>
                <p class="card-text"><strong>Date:</strong> <?php echo htmlspecialchars($transaction['event_date']); ?></p>
                <p class="card-text"><strong>Time:</strong> <?php echo htmlspecialchars($transaction['event_time']); ?></p>
                <p class="card-text"><strong>Location:</strong> <?php echo htmlspecialchars($transaction['location']); ?></p>
                <p class="card-text"><strong>Ticket Type:</strong> <?php echo htmlspecialchars($transaction['ticket_type']); ?></p>
                <p class="card-text"><strong>Quantity:</strong> <?php echo htmlspecialchars($transaction['quantity']); ?></p>
                <p class="card-text"><strong>Total Amount:</strong> $<?php echo htmlspecialchars($transaction['total_amount']); ?></p>
                <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
    
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
