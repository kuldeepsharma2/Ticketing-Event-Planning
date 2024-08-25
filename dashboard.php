<?php
// Start the session
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !$_SESSION['loggedin']) {
    // User is not logged in, redirect to login page
    header("Location: index.php");
    exit();
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Planner</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
    <script>
        // Pass PHP variable to JavaScript
        var loggedInUserId = <?php echo json_encode($_SESSION['user_id']); ?>;
    </script>
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
       
        <main class="container mt-4 flex-fill">
            <section>
                <h2 class="text-center">Upcoming Events</h2>
                <div class="events-container" id="eventsContainer">
                    <!-- Event cards will be populated here -->
                </div>
            </section>
        </main><footer class="footer bg-dark text-white text-center py-3 mt-4" style="padding: 1rem;text-align: center;margin-top: auto;margin-top: 0px !important;left: 0;bottom: 0;width: 100%;">
        <p>&copy; 2024 Shivam. All rights reserved.</p>
    </footer></div>
        
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="./script/script.js"></script>
</body>
</html>
