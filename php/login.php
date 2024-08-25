<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT id, username, password FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 's', $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_id, $db_username, $db_password);
    mysqli_stmt_fetch($stmt);

    if (password_verify($password, $db_password)) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $db_username;
        header('Location: dashboard.php');
    } else {
        echo "Invalid username or password.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);
}
?>
