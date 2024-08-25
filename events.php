<?php include './php/check_login.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Event</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="./css/styles.css">
    <style>
        .container {
            max-width: 600px;
            margin-top: 20px;
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
        
    <div class="container">
        <div class="card">
            <div class="card-body">
                <h1 class="card-title">Create Event</h1>
                <form id="eventForm" action="./php/create_event.php" method="POST">
                    <div class="form-group">
                        <label for="event_name">Event Name:</label>
                        <input type="text" class="form-control" id="event_name" name="event_name" required>
                    </div>

                    <div class="form-group">
                        <label for="event_date">Event Date:</label>
                        <input type="date" class="form-control" id="event_date" name="event_date" required>
                    </div>

                    <div class="form-group">
                        <label for="event_time">Event Time:</label>
                        <input type="time" class="form-control" id="event_time" name="event_time" required>
                    </div>

                    <div class="form-group">
                        <label for="location">Location:</label>
                        <input type="text" class="form-control" id="location" name="location" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description"></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Event</button>
                </form>
            </div>
        </div>
    </div>
    <footer class="footer bg-dark text-white text-center py-3 mt-4" style="padding: 1rem;text-align: center;margin-top: auto;margin-top: 0px !important;left: 0;bottom: 0;width: 100%;">
        <p>&copy; 2024 Shivam. All rights reserved.</p>
    </footer></div>
    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('eventForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    event.preventDefault(); // Prevent the default form submission

                    const formData = new FormData(this);

                    fetch('./php/create_event.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Show success message
                            alert(data.message);
                            // Redirect to dashboard after the user presses OK
                            window.location.href = 'dashboard.php'; // Update with the actual dashboard page URL
                        } else {
                            // Show error message
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error:', error));
                });
            } else {
                console.error('Form element not found.');
            }
        });
    </script>
</body>
</html>
