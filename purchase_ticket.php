<?php include './php/check_login.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include './php/database.php';
$event_id = $_GET['event_id'] ?? null;
$items_per_page = 5; // Number of tickets per page

if (!$event_id) {
    echo "Event ID is missing.";
    exit();
}

// Get the current page from query parameters
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Query to get the total number of tickets for pagination
$total_sql = "SELECT COUNT(*) as total FROM Tickets WHERE event_id = ? AND quantity > 0";
$total_stmt = $conn->prepare($total_sql);
$total_stmt->bind_param("i", $event_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_tickets = $total_row['total'];
$total_pages = ceil($total_tickets / $items_per_page);

// Query to get the tickets for the current page
$sql = "SELECT * FROM Tickets WHERE event_id = ? AND quantity > 0 LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $event_id, $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "No tickets available for this event.";
    $stmt->close();
    $conn->close();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Ticket</title>
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
        <h1>Purchase Ticket</h1>
        <div class="row">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4'>";
                echo "<div class='card mb-4 shadow-sm'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($row['ticket_type']) . "</h5>";
                echo "<p class='card-text'><strong>Price:</strong> $" . htmlspecialchars($row['price']) . "</p>";
                echo "<p class='card-text'><strong>Available Quantity:</strong> " . htmlspecialchars($row['quantity']) . "</p>";
                echo "<form action='./php/purchase_ticket_action.php' method='POST'>";
                echo "<input type='hidden' name='ticket_id' value='" . htmlspecialchars($row['ticket_id']) . "'>";
                echo "<div class='form-group'>";
                echo "<label for='quantity'>Quantity:</label>";
                echo "<input type='number' class='form-control' id='quantity' name='quantity' max='" . htmlspecialchars($row['quantity']) . "' required>";
                echo "</div>";
                echo "<button type='submit' class='btn btn-primary'>Purchase</button>";
                echo "</form>";
                echo "</div>";
                echo "</div>";
                echo "</div>";
            }

            $stmt->close();
            $conn->close();
            ?>
        </div>

        <!-- Pagination Controls -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mt-4">
                <li class="page-item <?php if ($page <= 1) echo 'disabled'; ?>">
                    <a class="page-link" href="?event_id=<?php echo htmlspecialchars($event_id); ?>&page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='?event_id=" . htmlspecialchars($event_id) . "&page=" . $i . "'>" . $i . "</a></li>";
                }
                ?>
                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?event_id=<?php echo htmlspecialchars($event_id); ?>&page=<?php echo $page + 1; ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
