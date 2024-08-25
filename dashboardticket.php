
<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['loggedin']) {
    // User is not logged in, redirect to login page
    header("Location: index.php");
    exit();
}
$user_id = $_SESSION['user_id'];

include './php/database.php';

// Pagination settings
$limit = 10; // Number of tickets per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

function getPagination($total_items, $current_page, $items_per_page) {
    $total_pages = ceil($total_items / $items_per_page);
    $pagination = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
    
    for ($i = 1; $i <= $total_pages; $i++) {
        $active_class = $i == $current_page ? 'active' : '';
        $pagination .= '<li class="page-item ' . $active_class . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
    }
    
    $pagination .= '</ul></nav>';
    return $pagination;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Organizer Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/style.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .pagination-container {
            margin-top: 1rem;
        }
    </style>
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
        <h1 class="text-center">Event Organizer Dashboard</h1>

        <div class="card my-4">
            <div class="card-header">
                <h2>Create Ticket</h2>
            </div>
            <div class="card-body">
                <form action="./php/create_ticket.php" method="POST">
                    <div class="form-group">
                        <label for="event_id">Event:</label>
                        <select class="form-control" id="event_id" name="event_id" required>
                            <?php
                            // Fetch events created by the logged-in user
                            $sql = "SELECT event_id, event_name FROM Events WHERE user_id = ?";
                            $stmt = $conn->prepare($sql);
                            $stmt->bind_param("i", $user_id);
                            $stmt->execute();
                            $result = $stmt->get_result();
                            while ($row = $result->fetch_assoc()) {
                                echo "<option value='" . $row['event_id'] . "'>" . htmlspecialchars($row['event_name']) . "</option>";
                            }
                            $stmt->close();
                            ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="ticket_type">Ticket Type:</label>
                        <input type="text" class="form-control" id="ticket_type" name="ticket_type" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="price">Price:</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="quantity">Quantity:</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </form>
            </div>
        </div>

        <div class="card my-4">
            <div class="card-header">
                <h2>Manage Tickets</h2>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="all-tickets-tab" data-toggle="tab" href="#all-tickets" role="tab" aria-controls="all-tickets" aria-selected="true">All Tickets</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="zero-quantity-tab" data-toggle="tab" href="#zero-quantity" role="tab" aria-controls="zero-quantity" aria-selected="false">Zero Quantity</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="in-use-tab" data-toggle="tab" href="#in-use" role="tab" aria-controls="in-use" aria-selected="false">In Use</a>
                    </li>
                </ul>
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="all-tickets" role="tabpanel" aria-labelledby="all-tickets-tab">
                        <div class="form-group my-2">
                            <input type="text" id="search-all" class="form-control" placeholder="Search by event name...">
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Event Name</th>
                                    <th>Ticket Type</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="ticket-table">
                                <?php
                                // Fetch all tickets with pagination
                                $sql = "SELECT t.ticket_id, e.event_name, t.ticket_type, t.price, t.quantity 
                                        FROM Tickets t 
                                        JOIN Events e ON t.event_id = e.event_id 
                                        WHERE e.user_id = ? 
                                        ORDER BY t.created_at DESC 
                                        LIMIT ? OFFSET ?";
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param("iii", $user_id, $limit, $offset);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['ticket_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['event_name']) . "</td>";
                                    echo "<td class='ticket-type'>" . htmlspecialchars($row['ticket_type']) . "</td>";
                                    echo "<td class='price'>" . htmlspecialchars($row['price']) . "</td>";
                                    echo "<td class='quantity'>" . htmlspecialchars($row['quantity']) . "</td>";
                                    echo "<td>
                                    <button class='btn btn-warning btn-sm edit-btn' data-id='" . htmlspecialchars($row['ticket_id']) . "'>Edit</button>
                                    <button class='btn btn-danger btn-sm delete-btn' data-id='" . htmlspecialchars($row['ticket_id']) . "'>Delete</button>
                                    <button class='btn btn-primary btn-sm update-btn' data-id='" . htmlspecialchars($row['ticket_id']) . "' style='display:none;'>Update</button>
                                    </td>";
                                    echo "</tr>";
                                }
                                $stmt->close();

                                // Get total count for pagination
                                $sql_count = "SELECT COUNT(*) as total FROM Tickets t JOIN Events e ON t.event_id = e.event_id WHERE e.user_id = ?";
                                $stmt_count = $conn->prepare($sql_count);
                                $stmt_count->bind_param("i", $user_id);
                                $stmt_count->execute();
                                $count_result = $stmt_count->get_result();
                                $total_items = $count_result->fetch_assoc()['total'];
                                $stmt_count->close();
                                ?>
                            </tbody>
                        </table>
                        <div class="pagination-container">
                            <?php echo getPagination($total_items, $page, $limit); ?>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="zero-quantity" role="tabpanel" aria-labelledby="zero-quantity-tab">
                        <div class="form-group my-2">
                            <input type="text" id="search-zero-quantity" class="form-control" placeholder="Search by event name...">
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Event Name</th>
                                    <th>Ticket Type</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="zero-quantity-table">
                                <?php
                                // Fetch tickets with zero quantity
                                $sql_zero = "SELECT t.ticket_id, e.event_name, t.ticket_type, t.price, t.quantity 
                                            FROM Tickets t 
                                            JOIN Events e ON t.event_id = e.event_id 
                                            WHERE e.user_id = ? AND t.quantity = 0 
                                            ORDER BY t.created_at DESC";
                                $stmt_zero = $conn->prepare($sql_zero);
                                $stmt_zero->bind_param("i", $user_id);
                                $stmt_zero->execute();
                                $result_zero = $stmt_zero->get_result();
                                while ($row_zero = $result_zero->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row_zero['ticket_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_zero['event_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_zero['ticket_type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_zero['price']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_zero['quantity']) . "</td>";
                                    echo "<td>
                                    <button class='btn btn-warning btn-sm edit-btn' data-id='" . htmlspecialchars($row_zero['ticket_id']) . "'>Edit</button>
                                    <button class='btn btn-danger btn-sm delete-btn' data-id='" . htmlspecialchars($row_zero['ticket_id']) . "'>Delete</button>
                                    <button class='btn btn-primary btn-sm update-btn' data-id='" . htmlspecialchars($row_zero['ticket_id']) . "' style='display:none;'>Update</button>
                                    </td>";
                                    echo "</tr>";
                                }
                                $stmt_zero->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="tab-pane fade" id="in-use" role="tabpanel" aria-labelledby="in-use-tab">
                        <div class="form-group my-2">
                            <input type="text" id="search-in-use" class="form-control" placeholder="Search by event name...">
                        </div>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Event Name</th>
                                    <th>Ticket Type</th>
                                    <th>Price</th>
                                    <th>Quantity</th>
                                </tr>
                            </thead>
                            <tbody id="in-use-table">
                                <?php
                                // Fetch tickets with quantity more than zero
                                $sql_in_use = "SELECT t.ticket_id, e.event_name, t.ticket_type, t.price, t.quantity 
                                            FROM Tickets t 
                                            JOIN Events e ON t.event_id = e.event_id 
                                            WHERE e.user_id = ? AND t.quantity > 0 
                                            ORDER BY t.created_at DESC";
                                $stmt_in_use = $conn->prepare($sql_in_use);
                                $stmt_in_use->bind_param("i", $user_id);
                                $stmt_in_use->execute();
                                $result_in_use = $stmt_in_use->get_result();
                                while ($row_in_use = $result_in_use->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row_in_use['ticket_id']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_in_use['event_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_in_use['ticket_type']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_in_use['price']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row_in_use['quantity']) . "</td>";
                                    echo "<td>
                                    <button class='btn btn-warning btn-sm edit-btn' data-id='" . htmlspecialchars($row_in_use['ticket_id']) . "'>Edit</button>
                                    <button class='btn btn-danger btn-sm delete-btn' data-id='" . htmlspecialchars($row_in_use['ticket_id']) . "'>Delete</button>
                                    <button class='btn btn-primary btn-sm update-btn' data-id='" . htmlspecialchars($row_in_use['ticket_id']) . "' style='display:none;'>Update</button>
                                    </td>";
                                    echo "</tr>";
                                }
                                $stmt_in_use->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    <script>
        $(document).ready(function() {
            function performSearch(searchInputId, tableId) {
                let searchValue = $(searchInputId).val().toLowerCase();
                $(tableId + ' tr').each(function() {
                    let eventName = $(this).find('td').eq(1).text().toLowerCase();
                    $(this).toggle(eventName.includes(searchValue));
                });
            }

            $('#search-all').on('input', function() {
                performSearch('#search-all', '#ticket-table');
            });

            $('#search-zero').on('input', function() {
                performSearch('#search-zero', '#zero-quantity-table');
            });

            $('#search-in-use').on('input', function() {
                performSearch('#search-in-use', '#in-use-table');
            });
        });
    </script>
<script>
$(document).ready(function() {
    // Handle Delete button click
    $('.delete-btn').on('click', function() {
        var ticket_id = $(this).data('id');
        console.log("Ticket ID to delete:", ticket_id); // Log the ticket ID to console

        if (ticket_id) {
            if (confirm('Are you sure you want to delete this ticket?')) {
                $.ajax({
                    url: './php/delete_ticket.php',
                    type: 'POST',
                    data: { ticket_id: ticket_id },
                    dataType: 'json',
                    success: function(response) {
                        console.log('Server response:', response); // Log server response
                        if (response.success) {
                            alert('Ticket deleted successfully');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        console.log("Response:", xhr.responseText); // Log the response for debugging
                    }
                });
            }
        } else {
            console.error("Invalid Ticket ID");
        }
    });

    // Handle Edit button click
    $('.edit-btn').on('click', function() {
        var ticket_id = $(this).data('id');
        console.log("Ticket ID to edit:", ticket_id); // Log the ticket ID to console

        var row = $(this).closest('tr');
        var ticketType = row.find('.ticket-type').text();
        var price = row.find('.price').text();
        var quantity = row.find('.quantity').text();

        row.find('.ticket-type').html('<input type="text" class="form-control" value="' + ticketType + '">');
        row.find('.price').html('<input type="number" step="0.01" class="form-control" value="' + price + '">');
        row.find('.quantity').html('<input type="number" class="form-control" value="' + quantity + '">');

        $(this).hide();
        row.find('.delete-btn').hide();
        row.find('.update-btn').show();
    });

    // Handle Update button click
    $('.update-btn').on('click', function() {
        var ticket_id = $(this).data('id');
        console.log("Ticket ID to update:", ticket_id); // Log the ticket ID to console

        var row = $(this).closest('tr');
        var ticketType = row.find('.ticket-type input').val();
        var price = row.find('.price input').val();
        var quantity = row.find('.quantity input').val();

        if (ticketType !== null && price !== null && quantity !== null) {
            $.ajax({
                url: './php/update_ticket.php',
                type: 'POST',
                data: { ticket_id: ticket_id, ticket_type: ticketType, price: price, quantity: quantity },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert('Ticket updated successfully');
                        location.reload();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error:", status, error);
                    console.log("Response:", xhr.responseText); // Log the response for debugging
                }
            });
        } else {
            console.error("Invalid Ticket Data");
        }
    });
});
</script>





</div>
</body>
</html>
