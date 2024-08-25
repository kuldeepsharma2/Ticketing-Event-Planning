<?php include './php/check_login.php'; ?>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include './php/database.php';

// Pagination variables
$items_per_page = 6; // Number of events per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Query to get the total number of events
$total_sql = "SELECT COUNT(DISTINCT E.event_id) AS total FROM Events E
               LEFT JOIN Tickets T ON E.event_id = T.event_id
               WHERE T.quantity > 0";
$total_result = $conn->query($total_sql);
$total_row = $total_result->fetch_assoc();
$total_events = $total_row['total'];
$total_pages = ceil($total_events / $items_per_page);

// Query to get the events for the current page
$sql = "SELECT E.event_id, E.event_name, E.event_date, E.event_time, E.location, E.description 
        FROM Events E
        LEFT JOIN Tickets T ON E.event_id = T.event_id
        WHERE T.quantity > 0
        GROUP BY E.event_id
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $items_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="./dashboard.php">Home <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./ticket_history.php">Purchase History</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="events.php">Add Events</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="./dashboardticket.php">Ticket Management</a>
                    </li>
                    <li class="nav-item  active">
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
        <h1 class="mb-4">Purchase Ticket</h1>
        <div class="row">
            <?php
            while ($row = $result->fetch_assoc()) {
                echo "<div class='col-md-4'>";
                echo "<div class='card mb-4 shadow-sm'>";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title'>" . htmlspecialchars($row['event_name']) . "</h5>";
                echo "<p class='card-text'><strong>Date:</strong> " . htmlspecialchars($row['event_date']) . "</p>";
                echo "<p class='card-text'><strong>Time:</strong> " . htmlspecialchars($row['event_time']) . "</p>";
                echo "<p class='card-text'><strong>Location:</strong> " . htmlspecialchars($row['location']) . "</p>";
                echo "<p class='card-text'><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
                echo "<a href='purchase_ticket.php?event_id=" . htmlspecialchars($row['event_id']) . "' class='btn btn-primary'>Purchase Ticket</a>";
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
                    <a class="page-link" href="?page=<?php echo $page - 1; ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php
                for ($i = 1; $i <= $total_pages; $i++) {
                    echo "<li class='page-item " . ($i == $page ? 'active' : '') . "'><a class='page-link' href='?page=" . $i . "'>" . $i . "</a></li>";
                }
                ?>
                <li class="page-item <?php if ($page >= $total_pages) echo 'disabled'; ?>">
                    <a class="page-link" href="?page=<?php echo $page + 1; ?>" aria-label="Next">
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
