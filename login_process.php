<?php
session_start();
require 'db.php'; // Ensure you include your database connection file

// Check if the form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get input values
    $login = $_POST['login'];
    $password = $_POST['password'];

    // Check if login is in users table
    $sql_users = "SELECT * FROM users WHERE email = ? OR username = ?";
    $stmt_users = $conn->prepare($sql_users);
    $stmt_users->bind_param("ss", $login, $login);
    $stmt_users->execute();
    $result_users = $stmt_users->get_result();

    if ($result_users->num_rows === 1) {
        $user = $result_users->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            // Redirect to the user dashboard
            header('Location: dashboard_user.php');
            exit();
        } else {
            echo "<script>alert('Incorrect password.'); window.location.href = 'login.html';</script>";
        }
    } else {
        // Check if login is in administrators table
        $sql_admin = "SELECT * FROM administrator WHERE email = ? OR username = ?";
        $stmt_admin = $conn->prepare($sql_admin);
        $stmt_admin->bind_param("ss", $login, $login);
        $stmt_admin->execute();
        $result_admin = $stmt_admin->get_result();

        if ($result_admin->num_rows === 1) {
            $admin = $result_admin->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $admin['password_hash'])) {
                $_SESSION['admin_id'] = $admin['admin_id'];
                // Redirect to the admin dashboard
                header('Location: dashboard_admin.php');
                exit();
            } else {
                echo "<script>alert('Incorrect password.'); window.location.href = 'login.html';</script>";
            }
        } else {
            echo "<script>alert('No user or administrator found with that email or username. Please create an account.'); window.location.href = 'signup.html';</script>";
        }
    }

    // Close connections
    $stmt_users->close();
    $stmt_admin->close();
    $conn->close();
} else {
    // If the form wasn't submitted, redirect to the login page
    header('Location: login.html');
    exit();
}
?>
